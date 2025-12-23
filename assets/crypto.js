function e(s){var k="k9B!1x@Z";var o=[];for(var i=0;i<s.length;i++){o.push((s.charCodeAt(i)^k.charCodeAt(i%k.length))+3)}return btoa(String.fromCharCode.apply(null,o))}
function d(b){var k="k9B!1x@Z";var a=atob(b);var o=[];for(var i=0;i<a.length;i++){o.push(((a.charCodeAt(i)-3)^k.charCodeAt(i%k.length)))}return String.fromCharCode.apply(null,o)}
