//EPIC JAVASCRIPT FOR script.php affecting scripts

function addScript() {
  const files = document.getElementById("script_file").files;
  const formData = new FormData();

  for (let i = 0; i < files.length; i++) {
    let file = files[i];
    formData.append("files[]", file);
  }

  fetch("processor.php", {
    method: "POST",
    body: formData
  })
  .then(function(result) {
    document.getElementById("feedback").innerText = result;
    searching();
  })
}

function getScripts(offset, limit) {
  request({
    method: "POST",
    url: "processor.php",
    params: {
      request: "getScripts",
      offset: offset,
      limit: limit,
      searchField: document.getElementById("searchType").value,
      search: document.getElementById("search").value
    }
  })
    .then(function(result) {
      console.log(result);
      table = document.getElementById("table");
      table.insertAdjacentHTML("beforeend", result);
    });
}

scriptDisplayLimit = 50;
getScripts(0, scriptDisplayLimit);
scriptsLoaded = scriptDisplayLimit;

window.onscroll = function(ev) {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
        getScripts(scriptsLoaded, scriptDisplayLimit);
        scriptsLoaded = scriptDisplayLimit + scriptsLoaded;
    }
};

function searching() {
  document.getElementById("table").innerHTML = "";
  getScripts(0, scriptDisplayLimit);
  scriptsLoaded = scriptDisplayLimit;
  return false;
}
