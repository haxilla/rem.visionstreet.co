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

         if (old_flyer_bg === '996600' ||
         old_flyer_bg === '990000' ||
         old_flyer_bg === '000066' ||
         old_flyer_bg === '000000' ) {
            console.log('Dark background detected, showing light colors');
            if(old_headline_bar_bg !== 'cccccc' && old_headline_bar_bg !== 'eeeeee' && old_flyer_bg !== 'ffffff'){
               console.log('Previous background was dark, resetting headline bar bg to light');
               document.querySelectorAll('.headline_bar_bg').forEach(el => {
                  el.style.backgroundColor = '#cccccc';
               });
            }else{
               console.log('Previous headline_bar_bg was light, no need to reset headline bar bg');
            };
         }else{
            console.log('Previous background was light, no need to reset headline bar bg');
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

});