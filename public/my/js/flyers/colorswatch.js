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

      const headlineGraphics = document.querySelectorAll('.hlGraphic');
      const firstHeadlineGraphic = headlineGraphics[0];
      const headline_graphic_url = firstHeadlineGraphic ? firstHeadlineGraphic.src : '';

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

            document.querySelectorAll('.hlGraphic').forEach(img => {
               if (img.src.includes('_333333_')) {
                  img.src = img.src.replace(
                     /_([0-9a-fA-F]{6})_/,
                     `_ffffff_`
                  );
               }
            });

         }else if (color === 'eeeeee') {         

            //background of accent bars
            console.log('Light background chosen, showing dark colors');

            document.querySelectorAll('.accent_bars').forEach(el => {
               el.style.backgroundColor = '#333333';
            });
            //text separator
            document.querySelectorAll('.accent_bars').forEach(el => {
               el.style.color = '#ffffff';
            });
            //hyperlinks
            document.querySelectorAll('.accent_link').forEach(el => {
               el.style.color = '#ffffff';
            });

            document.querySelectorAll('.hlGraphic').forEach(img => {
               if (img.src.includes('_ffffff_')) {
                  img.src = img.src.replace(
                     /_([0-9a-fA-F]{6})_/,
                     `_333333_`
                  );
               }
            });

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
            document.querySelectorAll('.accent_bars').forEach(el => {
               el.style.color = '#333333';
            });
            document.querySelectorAll('.accent_link').forEach(el => {
               el.style.color = '#333333';
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