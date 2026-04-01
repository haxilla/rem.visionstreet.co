document.addEventListener('DOMContentLoaded', function () {

   function rgbToHex(rgbString) {
      const values = rgbString.match(/\d+/g);
      if (!values) return null;

      return values
         .slice(0, 3)
         .map(v => Number(v).toString(16).padStart(2, "0"))
         .join("");
   }

   function applySwatch(sw) {

      // values from swatch
      var style   =  sw.dataset.style;
      var scheme  =  sw.dataset.scheme;
      var color   =  sw.dataset.color;

      //get current headline graphic URL
      const img = document.querySelector('.hlGraphic');
      const headline_graphic_url = img.src;

      //get current headline bar bg color
      const headline_bar_bg = window.getComputedStyle(
         document.querySelector('.headline_bar_bg')
      ).backgroundColor;

      //get current headline bar bg color
      const flyer_bg = window.getComputedStyle(
         document.querySelector('.flyer_background')
      ).backgroundColor;
      

      //convert to hex
      const old_headline_bar_bg = rgbToHex(headline_bar_bg);
      const old_flyer_bg = rgbToHex(flyer_bg);

      console.log(old_headline_bar_bg,old_flyer_bg);
      console.log(headline_graphic_url);
      console.log('Applying swatch:', { style, color, scheme });


      // --------------------------
      // APPLY COLORS
      // --------------------------

      if(style=='background'){
         document.querySelectorAll('.flyer_background').forEach(el => {
            el.style.backgroundColor = '#' + color;
         });

         document.querySelectorAll('.accent_bars').forEach(el => {
            el.style.backgroundColor = '#' + color;
         });

         document.querySelectorAll('.flyer_background_border, .flyer_border').forEach(function (el) {
            el.style.border = '1px solid #' + color;
         });

         if(color !== 'cccccc' && color !== 'eeeeee'){
            document.querySelectorAll('.headline_text').forEach(el => {
               el.style.color = '#ffffff';
            });
            
         }else{
            document.querySelectorAll('.headline_text').forEach(el => {
               el.style.color = '#333333';
            });
         }

         if (flyer_background === '996600' ||
         flyer_background === '990000' ||
         flyer_background === '000066' ||
         flyer_background === '000000' ) {

            if(old_headline_bar_bg !== 'cccccc' && old_headline_bar_bg !== 'eeeeee' && old_flyer_bg !== 'ffffff'){
               document.querySelectorAll('.headline_bar_bg').forEach(el => {
                  el.style.backgroundColor = '#cccccc';
               }
         }
   }

   // --------------------------
   // CLICK HANDLER
   // --------------------------

   document.querySelectorAll('.colorswatch').forEach(function (sw) {
      sw.addEventListener('click', function (e) {
         e.preventDefault();
         console.log('Swatch clicked:', this);
         applySwatch(this);
      });
   });

/*
      if (flyer_background) {
         document.querySelectorAll('.flyer_background').forEach(function (el) {
            el.style.backgroundColor = '#' + flyer_background;
         });

         document.querySelectorAll('.flyer_background_border, .flyer_border').forEach(function (el) {
            el.style.border = '1px solid #' + flyer_background;
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
*/
});