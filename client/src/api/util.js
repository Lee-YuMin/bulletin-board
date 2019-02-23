var API = {};

API.property._getIp = function() {
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

function throttle(fn, delay, me) {
    let timer
    return function() {
        if (!timer){
            timer = setTimeout(() => {
                timer = null
                fn.apply(me, arguments)
            }, delay)
        }
    }
};