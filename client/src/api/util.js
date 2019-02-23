var API = {};

// [원본 API] https://medium.com/@enro2414_40667/javascript-%EB%A1%9C-%EC%99%B8%EB%B6%80ip-%EA%B3%B5%EC%9D%B8ip-%EA%B0%80%EC%A0%B8%EC%98%A4%EA%B8%B0-f6531630cf30
// (function(window){
//     var head = document.getElementsByTagName('head')[0];
//     var script= document.createElement('script');
//     window.getIP = function(json) {
//       console.log(json);    
//       //head.removeChild(script); // Optional
//     };
//     script.type= 'text/javascript';
//     script.src= 'https://api.ipify.org?format=jsonp&callback=getIP';
//     head.appendChild(script);
//   })(window);

API.getIp = function() {
    return new Promise((resolve, resject) => {
        var head = document.getElementsByTagName('head')[0];
        var script= document.createElement('script');
        window.getIP = function(json) {
            head.removeChild(script);
            resolve(json);
        };
        script.type= 'text/javascript';
        script.src= 'https://api.ipify.org?format=jsonp&callback=getIP';
        head.appendChild(script);
      })
};