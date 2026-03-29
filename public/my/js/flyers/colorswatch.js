document.addEventListener('DOMContentLoaded', function () {

   function normalizeHex(val) {
      return (val || '').replace('#', '').trim();
   }

   function setDisplay(selector, value) {
      document.querySelectorAll(selector).forEach(function (el) {
         el.style.display = value;
      });
   }

   function applySwatch(sw) {

      console.log(sw.dataset);
      // values from swatch
      var flyer_background  = normalizeHex(sw.dataset.flyerBackground);
      var headline_text     = normalizeHex(sw.dataset.headlineText);
      var headline_bar_bg   = normalizeHex(sw.dataset.headlineBarBg);
      var headline_bar_text = normalizeHex(sw.dataset.headlineBarText);
      var accent_text       = normalizeHex(sw.dataset.accentText);
      var accent_bars       = normalizeHex(sw.dataset.accentBars);
      var graphic_textcolor = normalizeHex(sw.dataset.graphicTextcolor);

      var hlGraphic   = sw.dataset.hlGraphic || '';
      var noDark      = parseInt(sw.dataset.noDark || '0', 10);
      var noLight     = parseInt(sw.dataset.noLight || '0', 10);

      // --------------------------
      // APPLY COLORS
      // --------------------------

      if (flyer_background) {
         document.querySelectorAll('.flyer_background').forEach(function (el) {
            el.style.backgroundColor = '#' + flyer_background;
         });

         document.querySelectorAll('.flyer_background_border, .flyer_border').forEach(function (el) {
            el.style.border = '1px solid #' + flyer_background;
         });

         document.querySelectorAll('#theBackground').forEach(function (el) {
            el.value = flyer_background;
         });
      }

      if (headline_bar_bg) {
         document.querySelectorAll('.headline_bar_bg').forEach(function (el) {
            el.style.backgroundColor = '#' + headline_bar_bg;
         });
      }

      if (headline_bar_text) {
         document.querySelectorAll('.headline_bar_text').forEach(function (el) {
            el.style.color = '#' + headline_bar_text;
         });
      }

      if (headline_text) {
         document.querySelectorAll('.headline_text, .jqHeadlineDiv').forEach(function (el) {
            el.style.color = '#' + headline_text;
         });
      }

      if (accent_text) {
         document.querySelectorAll('.accent_text').forEach(function (el) {
            el.style.color = '#' + accent_text;
         });
      }

      if (accent_bars) {
         document.querySelectorAll('.accent_bars').forEach(function (el) {
            el.style.backgroundColor = '#' + accent_bars;
         });
      }

      if (graphic_textcolor) {
         var g = document.getElementById('graphic_textcolor');
         if (g) g.style.color = '#' + graphic_textcolor;

         document.querySelectorAll('#theColorID').forEach(function (el) {
            el.value = graphic_textcolor;
         });
      }

      // --------------------------
      // GRAPHIC IMAGE
      // --------------------------

      if (hlGraphic) {
         document.querySelectorAll('.hlGraphic').forEach(function (el) {
            el.setAttribute('src', hlGraphic);
         });
      }

      // --------------------------
      // DARK / LIGHT LOGIC
      // --------------------------

      if (noDark > 0) {
         setDisplay('.noDark', 'none');
      } else {
         document.querySelectorAll('.noDark, .darkColors').forEach(function (el) {
            el.style.display = '';
         });
      }

      if (noLight > 0) {
         setDisplay('.noLight', 'none');
      } else {
         setDisplay('.noLight', '');
      }

      // --------------------------
      // BACKGROUND-BASED TOGGLE (legacy logic preserved)
      // --------------------------

      if (
         flyer_background === '996600' ||
         flyer_background === '990000' ||
         flyer_background === '999999' ||
         flyer_background === '000066' ||
         flyer_background === '000000' ||
         flyer_background === '333333'
      ) {
         setDisplay('.lightColors', '');
         setDisplay('.darkColors', 'none');
      } else if (flyer_background) {
         setDisplay('.lightColors', 'none');
         setDisplay('.darkColors', '');
      }
   }

   // --------------------------
   // CLICK HANDLER
   // --------------------------

   document.querySelectorAll('.colorswatch').forEach(function (sw) {
      sw.addEventListener('click', function (e) {
         e.preventDefault();
         applySwatch(this);
      });
   });

   // --------------------------
   // INITIAL STATE CHECK
   // --------------------------

   var bgInput = document.querySelector('#theBackground');
   var bg = bgInput ? normalizeHex(bgInput.value) : '';

   if (
      bg === '996600' ||
      bg === '990000' ||
      bg === '999999' ||
      bg === '000066' ||
      bg === '000000' ||
      bg === '333333'
   ) {
      setDisplay('.lightColors', '');
      setDisplay('.darkColors', 'none');
   } else {
      setDisplay('.lightColors', 'none');
      setDisplay('.darkColors', '');
   }

});