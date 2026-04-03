document.addEventListener('DOMContentLoaded', function () {

   function rgbToHex(rgbString) {
      const values = rgbString.match(/\d+/g);
      if (!values) return null;

      return values
         .slice(0, 3)
         .map(v => Number(v).toString(16).padStart(2, "0"))
         .join("");
   }

   function updateColorInUrl(url, hex) {
      return url.replace(/_(.*?)_/, `_${hex}_`);
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

      //get current headline bar bg color
      const accent_bars = window.getComputedStyle(
         document.querySelector('.accent_bars')
      ).backgroundColor;
      

      //convert to hex
      const old_headline_bar_bg = rgbToHex(headline_bar_bg);
      const old_flyer_bg = rgbToHex(flyer_bg);
      const old_accent_bars = rgbToHex(accent_bars);

      console.log(old_headline_bar_bg,old_flyer_bg);
      console.log(headline_graphic_url);
      console.log(old_accent_bars);
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

         if(color !== 'cccccc' && color !== 'eeeeee' && color !== '999999'){
            document.querySelectorAll('.headline_text').forEach(el => {
               el.style.color = '#ffffff';
            });
            
         }else{
            document.querySelectorAll('.headline_text').forEach(el => {
               el.style.color = '#333333';
            });
         }

         if (color === '996600' ||
         color === '990000' ||
         color === '000066' ||
         color === '000000' ) {

            console.log('Dark background chosen, showing light colors');
            // hide light accents
            document.querySelectorAll('.light-accents').forEach(el => {
               el.style.display = '';
            });

            // show dark accents
            document.querySelectorAll('.dark-accents').forEach(el => {
               el.style.display = 'none';
            });

            document.querySelectorAll('.headline_bar_text').forEach(el => {
               el.style.color = '#' + color;
            });

            if(old_headline_bar_bg !== 'ffffff' && old_headline_bar_bg !== 'eeeeee' && old_headline_bar_bg !== 'ffffcc'){

               document.querySelectorAll('.headline_bar_bg').forEach(el => {
                  el.style.backgroundColor = '#eeeeee';
               });

            }else{
               console.log('leave headline bar bg as is, already light');
            };
         }else if(color==='999999'){
            console.log('Medium background chosen, showing light colors'); 
            document.querySelectorAll('.headline_bar_bg').forEach(el => {
               el.style.backgroundColor = '#333333';
            });
            document.querySelectorAll('.headline_bar_text').forEach(el => {
               el.style.color = '#ffffff';
            });
            document.querySelectorAll('.headline_text').forEach(el => {
               el.style.color = '#ffffff';
            });

            // hide light accents
            document.querySelectorAll('.light-accents').forEach(el => {
               el.style.display = 'none';
            });

            // show dark accents
            document.querySelectorAll('.dark-accents').forEach(el => {
               el.style.display = '';
            });

            if(headline_graphic_url.includes('_333333_')){

               const img = document.querySelector('.hlGraphic');
               // set new color from dark to light
               let newColor = 'ffffff';
                // or from your swatch - you could also have a data attribute on the swatch for this

               // replace the color in the URL
               const updatedUrl = headline_graphic_url.replace(
                  /_([0-9a-fA-F]{6})_/,
                  `_${newColor}_`
               );

               img.src = updatedUrl;
            }

         }else if (color === 'eeeeee') {         

            document.querySelectorAll('.accent_bars').forEach(el => {
               el.style.backgroundColor = '#333333';
            });

            if(headline_graphic_url.includes('_ffffff_')){

               const img = document.querySelector('.hlGraphic');

               // set new color from white to dark
               let newColor = '333333'; // or from your swatch

               // replace the color in the URL
               const updatedUrl = headline_graphic_url.replace(
                  /_([0-9a-fA-F]{6})_/,
                  `_${newColor}_`
               );

               // apply it back
               img.src = updatedUrl;}

            // hide light accents
            document.querySelectorAll('.light-accents').forEach(el => {
               el.style.display = 'none';
            });

            // show dark accents
            document.querySelectorAll('.dark-accents').forEach(el => {
               el.style.display = '';
            });

         }else{

            document.querySelectorAll('.headline_bar_bg').forEach(el => {
               el.style.backgroundColor = '#333333';
            });
            document.querySelectorAll('.headline_bar_text').forEach(el => {
               el.style.color = '#ffffff';
            });
            
            // hide light accents
            document.querySelectorAll('.light-accents').forEach(el => {
               el.style.display = 'none';
            });

            // show dark accents
            document.querySelectorAll('.dark-accents').forEach(el => {
               el.style.display = '';
            });

            console.log('final else');
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