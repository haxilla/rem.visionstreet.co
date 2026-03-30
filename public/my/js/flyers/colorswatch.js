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

      // values from swatch
      var style   =  sw.dataset.style;
      var scheme  =  sw.dataset.scheme;
      var color   =  sw.dataset.color;

      //get current headline graphic URL
      const img = document.querySelector('.hlGraphic');
      const headline_graphic_url = img.src;

      console.log(headline_graphic_url);
      console.log('Applying swatch:', { style, color, scheme });

      // --------------------------
      // APPLY COLORS
      // --------------------------

      if(style=='background'){
         document.querySelectorAll('.flyer_background').forEach(el => {
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
      }
   }

});