import { renderHTML, renderJSON, handleFormSubmission, buildEndpoint } from './utils.js';

//wrapped in IIFE or top level return will error
(() => {

  if (!document.body.classList.contains('linkcheck')) {
    return;}

  // ✅ load handler maps once (keeps original behavior)
  // eager: synchronous access for click + after-hook
  const eagerModules = import.meta.glob('./handlers/*.js', { eager: true });
  // lazy: returns loader functions for autosuggest input
  const lazyModules  = import.meta.glob('./handlers/*.js');

  document.addEventListener('click', (e) => {

    let handled=false;

    const a = e.target.closest('a');
    if (!a) return;

    //gather all datasets - check action
    const postData = { ...a.dataset };
    console.log('original postData: ',postData);

    const action   = postData.action;
    //fail if no action set
    if (!action) return;
    //get all modules
    const modulePath = `./handlers/${action}.js`;
    // Look up the matching handler module for this data-action
    // Example: data-action="textedit" → ./handlers/textedit.js
    // Call the default export from that module and pass it (a = clicked <a>, e = event)
    if (eagerModules[modulePath]?.default) {
      eagerModules[modulePath].default(a, e, postData);
      handled = true;}

    //handle
    if (action === 'handle') {
      e.preventDefault();

      //deconstruct: populates postData into corresponding variables
      const { renderto, renderas, renderfrom, route, uuid, token } = postData;

      //error if missing required vars
      if (!renderto || !renderas || !renderfrom) {
        alert('Missing renderto/renderas/renderfrom');
        return;}

      //start endpoint var
      let endpoint;                                   
      if (route && route.trim()) {
        endpoint = route.trim();
      } else {
        endpoint = buildEndpoint(renderfrom, uuid, token);}

      //choose function to run
      if (renderas === 'html') {
        renderHTML(postData, endpoint).then(async () => {
          // Generic after-swap hook (optional)
          const after = (postData.after || '').trim();
          if (!after) return;
          const modulePath = `./handlers/${after}.js`;

          if (eagerModules[modulePath]?.default) {
            try {
              // pass the same signature as other handlers
              eagerModules[modulePath].default(a, e, postData);
            } catch (err) {
              console.error('after hook failed:', after, err);
            }
          }
        });
      } else if (renderas === 'json') {
        renderJSON(postData, endpoint);   // if you use JSON responses
      } else {
        alert('Unknown renderas');}

      handled = true;}


    if(action=='toggle'){

      const toggleClass = e.target.dataset.toggle;
      const hideClass = e.target.dataset.hide;

      if(toggleClass=='this'){
        document.querySelectorAll('[class*="droptarget-"]').forEach(el => el.style.display = 'none');
        document.querySelectorAll('[class*="togglemenu-"]').forEach(el => el.style.display = 'flex');
        return;}

      const toggleEl = document.querySelector(`.${toggleClass}`);
      const hideEl = hideClass ? document.querySelector(`.${hideClass}`) : null;

      if (!toggleEl){
        console.log(toggleClass);
        alert('error-line73-handle.js - No Element for toggle class'); 
        return;} 

      // This is the FIX: match class *anywhere* in the class list
      document.querySelectorAll('[class*="droptarget-"]').forEach(el => el.style.display = 'none');
      document.querySelectorAll('[class*="togglemenu-"]').forEach(el => el.style.display = 'flex');

      toggleEl.style.display = 'block';

      if (hideEl) {
        hideEl.style.display = 'none';};

      handled=true;}

    if(action=='modal'){

      alert('Modal action triggered! This is a placeholder. Implement modal logic here.');


      handled=true;}

    if (!handled) {
      console.warn('No action found for', action);
      alert('No action found for', action);}
    
  });

  document.addEventListener('submit', (e) => {
    
    const form = e.target;

    //ensure its really a form
    if (!(form instanceof HTMLFormElement)){
      console.log('error-line66-handler.js - Not a form element');
      return;}

    // 🚨 Bypass everything if form data-bypass=1
    // don't use handler.js let browser handle it
    if (form.dataset.bypass === "1") {
      console.log("Bypassing handler because form bypass=1");
      return; }

    // Otherwise, stop default submission
    e.preventDefault();

    if (form.dataset.action !== 'handle'){
      console.log('error-line69-hander.js - Not setup to handle in dataset');
      alert('forms diverted but not configured with data-action');
      return;} 

    console.log('using handle Form submission');
    handleFormSubmission(form);

  });

  document.addEventListener('input', (e) => {
    const el = e.target;
    //console.log('line129-handle.js input event on', el);

    if (!(el instanceof HTMLInputElement)) return;
    //console.log('line132-handle.js input event on input element', el);
    // opt-in only
    
    const action = el.dataset.action;
    //console.log('line136-handle.js input element with data-action', action);

    if (action !== 'autosuggest') return;
    //console.log('line139-handle.js autosuggest input event on', el,action);

    const modulePath = `./handlers/${action}.js`;

    if (!lazyModules[modulePath]) return;
    
    lazyModules[modulePath]().then((m) => {
      if (m?.default) m.default(el, e, { ...el.dataset, textvalue: el.value });
    });
    
  });

})();
