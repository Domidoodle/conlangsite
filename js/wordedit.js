//Javascript for wordedit.php
//mostly deals with ajax requests
//request function is in main.js!

function addMeaning() {
  request({
    method: "POST",
    url: "processor.php",
    params: { //params object is made into string, is very nice
      request: "addMeaning",
      w: params.get("w"),
    }
  })
    .then(function(result) {
      button = document.getElementById("addMeaning");
      button.insertAdjacentHTML("beforebegin", result);
    });

}

function saveMeaning() {
  firer = getFirer();
  request({
    method: "POST",
    url: "processor.php",
    params: { //params object is made into string, is very nice
      request: "saveMeaning",
      m: firer.parentElement.id,
      field: firer.name,
      value: firer.value.replace("<br />", "")
    }
  });
}

function delMeaning() {
  if(confirm("Are you sure you want to delete this meaning?")) {
    firer = getFirer();
    request({
      method: "POST",
      url: "processor.php",
      params: { //params object is made into string, is very nice
        request: "delMeaning",
        m: firer.parentElement.id
      }
    })
      .then(function(result) {
        console.log(result);
      });
    firer.parentElement.remove();
  }
}

function save() {
  firer = getFirer();
  request({
    method: "POST",
    url: "processor.php",
    params: { //params object is made into string, is very nice
      request: "save",
      w: params.get("w"),
      field: firer.name,
      value: firer.value.replace("<br />", "")
    }
  })
    .then(function(result) {
      console.log(result);
    });
}

//delete word
function deleteWord() {
  if(confirm("Are you sure you want to delete this word?")) {
    request({
      method: "POST",
      url: "processor.php",
      params: { //params object is made into string, is very nice
        request: "save",
        w: params.get("w"),
        field: firer.name,
        value: firer.value
      }
    })
      .then(function(result) {
        console.log(result);
      });
  }
}

//getMeanings request
request({
  method: "POST",
  url: "processor.php",
  params: { //params object is made into string, is very nice
    request: "getMeanings",
    w: params.get("w")
  }
})
  .then(function(result) {
    button = document.getElementById("addMeaning");
    button.insertAdjacentHTML("beforebegin", result);
  });
