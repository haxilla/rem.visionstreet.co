export function setKeyValues(str) {
  if (!str) return {};
  return Object.fromEntries(
    str.split(';')
       .filter(Boolean)
       .map(p => {
         const [k, v] = p.split(':').map(s => s.trim());
         return [k, v];
       })
  );
}

export function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = el.scrollHeight + 'px';}

// Small, focused helper
export function buildEndpoint(renderfrom, uuid = '', token = '') {
  // base path from renderfrom (+ optional uuid)
  const firstSeg  = renderfrom.split('.', 1)[0];
  const routePath = '/' + renderfrom.replace(/\./g, '/');

  // prefix map
  const prefixMap = { 
    postgres: '/admin/data', 
    mysql:    '/admin/data',
    projects: '/admin',
    laravel:  '/admin',
    task:     '/admin/projects',
    comments: '/admin/projects',};
  const prefix    = prefixMap[firstSeg] || '';
  const needsPref = prefix && !routePath.startsWith(prefix + '/');

  //set base endpoint
  let endpoint =
    (needsPref ? prefix : '') +
    routePath +
    (uuid ? '/' + encodeURIComponent(uuid) : '');

  //no its append encrypted token as ?p=...
  if (token) {
    const u = new URL(endpoint, window.location.origin);
    u.searchParams.set('p', token);    
    endpoint = u.pathname + u.search;}

  return endpoint;}

  // decrypt
  export async function decryptToken(token, endpoint = '/admin/decrypt') {
    if (!token) throw new Error('decryptToken: missing token');

    const csrf =
      document.querySelector('meta[name="csrf-token"]')?.content || '';

    const res = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
        ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
        'X-Requested-With': 'fetch',
      },
      body: new URLSearchParams({ token }).toString(),
      credentials: 'same-origin',
    });

    if (!res.ok) {
      const text = await res.text().catch(() => '');
      throw new Error(`decryptToken: ${res.status} ${res.statusText} ${text}`);}

    return res.json();}


// GET HTML swap. Expects a fully-built endpoint.
export function renderHTML(postData, endpoint) {

  if (!endpoint) {
    console.error('renderHTML: missing endpoint');
    alert('Missing endpoint');
    return;}

  // Always GET, no body, no CSRF header.
  return fetch(endpoint, {
    method: 'GET',
    credentials: 'same-origin',
    headers: { 
      'Accept': 'text/html',
      'X-Pageswap': '1',},
  })
    .then(async (res) => {
      if (!res.ok) throw new Error(`${res.status} ${res.statusText}\n${await res.text()}`);
      return res.text();
    })
    .then((html) => {
      // Fallback for full pages
      if (/\<\!doctype html/i.test(html) || /\<html[\s>]/i.test(html)) {
        // Server returned a full page (not a fragment) — do a normal navigation.
        alert('redirected!');
        window.location.href = endpoint;
        return;}

      //check for renderto container
      const target = document.querySelector(`.${postData.renderto}`);
      if (!target) {
        console.warn(`⚠️ Target container .${postData.renderto} not found`);
        return;}

      //replace the container
      target.innerHTML = html;
      // Re-init Alpine (if present)
      if (window.Alpine?.initTree) Alpine.initTree(target);

      //push changes to browser
      const url = new URL(endpoint, location.origin);
      history.pushState({}, '', url.pathname + url.search + url.hash);

    })
    .catch((err) => console.error('renderHTML (GET) failed:', err));}

export function postHTML(postData,endpoint) {
  //console.log('postData from renderHTML function: ',postData);
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const renderto=postData.renderto;
  const renderfrom=postData.renderfrom;
  //const pushURL=postData.pushURL;
  // remove last segment after dot 
  // (avoids exposing action in url)
  const returnDot=renderfrom.replace(/\.[^.]+$/, '');
  // build endpoints
  const returnPath   = '/' + returnDot.replace(/\./g, '/');

  fetch(endpoint, {
    method: 'POST',
    body: new URLSearchParams(postData).toString(),
    headers: {
      'X-Pageswap': '1',
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
    },
  })
  .then(res => res.text())
  .then(html => {
    //check contents and any full page redirects
    //unless its an error page (page title=laravel)
    if ((/<!doctype html/i.test(html) || /<html[\s>]/i.test(html)) 
        && !/<title[^>]*>\s*Laravel\s*<\/title>/i.test(html)) {
      window.location.href = endpoint;
      return;}

    //check for renderto container
    const target = document.querySelector(`.${postData.renderto}`);
    if (!target) {
      console.warn(`⚠️ Target container .${postData.renderto} not found`);
      return;}

    //replace the container
    target.innerHTML = html;
    // Re-init Alpine (if present)
    if (window.Alpine?.initTree) Alpine.initTree(target);

    //push changes to browser
    const url = new URL(returnPath, location.origin);
    history.pushState({}, '', url.pathname + url.search + url.hash);

    //check values
    console.log(renderfrom,endpoint,url,location.origin,returnPath);

  })
  .catch(err => {
    console.error('💥 Handle request error:', err);
  });
}

export function renderJSON(postData,endpoint) {
  fetch(endpoint, {
    method: 'POST',
    body: postData.toString(),
    headers: {
      'X-Pageswap': '1',
      'Content-Type': 'application/x-www-form-urlencoded',
      ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
    },
  })
  .then(async res => {
    if (!res.ok) {
      const errorText = await res.text();
      console.error(`🚨 JSON Error ${res.status}:\n`, errorText);
      return;
    }

    const json = await res.json();
    console.log('✅ JSON response:', json);

    if (json?.funcName) {
      const fn = handlers[json.funcName];
      if (typeof fn === 'function') {
        try {
          fn();
        } catch (err) {
          console.error('⚠️ Handler error for', json.funcName, err);
        }
      } else {
        console.warn('⚠️ No handler defined for', json.funcName);
      }
    }
  })
  .catch(err => {
    console.error('❌ JSON Fetch failed:', err.message);
  });
}

export function handleFormSubmission(form) {
  const fields   = Object.fromEntries(new FormData(form).entries());
  const postData = { ...fields, ...form.dataset };

  // Guard: need renderto + renderas + (route OR renderfrom)
  if (!postData.renderto || !postData.renderas || !postData.prefix
  || (!postData.route && !postData.renderfrom)) {
    alert('Missing params: need renderto, renderas, prefix and either route or renderfrom');
    return;}

  //deconstruct: populates postData into corresponding variables
  const { renderto, renderas, renderfrom, route, prefix, uuid, token } = postData;

  //start endpoint var
  const endpoint="/"+prefix+"/form";

  if (renderas === 'html') {
    postHTML(postData, endpoint);
  } else if (renderas === 'json') {
    postJSON(postData, endpoint);
  } else {
    console.warn('⚠️ Unknown renderAs:', postData.renderas);
  }
}
