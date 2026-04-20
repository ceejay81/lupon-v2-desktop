/**
 * Local Phosphor Icons Loader
 * Loads all icon weights from local paths (no CDN)
 */
(function() {
  var head = document.getElementsByTagName("head")[0];

  // Find the script tag to determine base path
  var scripts = document.getElementsByTagName("script");
  var basePath = "/vendor/phosphor-icons/";

  for (var i = 0; i < scripts.length; i++) {
    var src = scripts[i].src;
    if (src && src.indexOf("phosphor-icons") !== -1) {
      // Extract base path from script src
      var match = src.match(/^(.*\/phosphor-icons\/)/);
      if (match) {
        basePath = match[1];
        break;
      }
    }
  }

  // Remove domain if present to get relative path
  if (basePath.indexOf("http") === 0) {
    var url = new URL(basePath);
    basePath = url.pathname;
  }

  var weights = ["regular", "thin", "light", "bold", "fill", "duotone"];
  for (var j = 0; j < weights.length; j++) {
    var link = document.createElement("link");
    link.rel = "stylesheet";
    link.type = "text/css";
    link.href = basePath + weights[j] + "/style.css";
    head.appendChild(link);
  }
})();
