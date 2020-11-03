function addLanguage() {

  named = document.getElementById("named")
  pronunciation = document.getElementById("pronunciation")
  name_romanised = document.getElementById("name_romanised")
  script_id = document.getElementById("script_id")


  request({
    method: "POST",
    url: "processor.php",
    params: { //params object is made into string, is very nice
      request: "addLanguage",
      name: named.value,
      pronunciation: pronunciation.value,
      name_romanised: name_romanised.value,
      script_id: script_id.value
    }
  })
    .then(function(result) {
      console.log(result);
      result = JSON.parse(result);
      console.log(result);
      location.search = "l=" + result.id;
    });
}

function save() {
  firer = getFirer();
  request({
    method: "POST",
    url: "processor.php",
    params: { //params object is made into string, is very nice
      request: "updateLanguage",
      l: params.get("l"),
      field: firer.name,
      value: firer.value
    }
  });
}

function deleteLanguage() {
  if(confirm("Are you sure you want to delete this language?")) {
    request({
      method: "POST",
      url: "processor.php",
      params: { //params object is made into string, is very nice
        request: "deleteLanguage",
        l: params.get("l"),
      }
    });
  }
}
