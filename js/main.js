let params = new URLSearchParams(location.search);
//js = php
//params.get("key") = $_GET["key"]


function getFirer() { //gets HTML element that fired the event
  return event.target || event.srcElement;
}


function request(options) { //does ajax requests example of request below function
  return new Promise(function(resolve, reject) {
    var xhr = new XMLHttpRequest();
    xhr.onload = function() {
      resolve(this.responseText); //resolve promise with reponseText
    };
    xhr.onerror = reject; //rip promise
    xhr.open(options.method, options.url, true);
    if (options.method == "POST") { //only needs this if method is POST but unsure what does if method is GET
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    }
    var params = options.params;
    if (params && typeof params === 'object') {
      params = Object.keys(params).map(function(key) { //code to put params object into string like seen in GET URL
        return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
      }).join('&')
    }
    xhr.send(params);
    //console.log("Executed AJAX request with options:");
    //console.log(options);
  });
}

/* example request
request({
  method: "GET",
  url: "text.txt",
  params: { //params object is made into string, is very nice
    test: 1 //parameters that would normally in the URL for a request with a GET method
  }
})
  .then(function(result) {
    console.log(result); //result is reponseText from ajax request
  })
  .catch(function () { //if it fails for some reason
    console.error("OH HECK ERROR");
  });
*/


//user register
function addUser() {
  email = document.getElementById("email");
  uname = document.getElementById("uname");
  pwd = document.getElementById("passw");

  if (email.value == "" || uname.value == "" || pwd.value == "") {
    document.getElementById("feedback").innerText = "All fields must be filled";
    return 1;
  }

  request({
    method: "POST",
    url: "processor.php",
    params: {
      request: "addUser",
      email: email.value,
      uname: uname.value,
      pwd: pwd.value
    }
  })
    .then(function(result) {
      result = JSON.parse(result);
      if(result.success) {
        location.replace(result.redirect);
      } else {
        document.getElementById("feedback").innerText = result.error;
      }
    });
}

//user logout
function destroyUser() {
  request({
    method: "POST",
    url: "processor.php",
    params: {
      request: "destroyUser"
    }
  })
    .then(function(result) {
      location.replace(location);
    });
}

//user login
function checkUser() {
  uname = document.getElementById("uname");
  pwd = document.getElementById("passw");

  request({
    method: "POST",
    url: "processor.php",
    params: {
      request: "checkUser",
      uname: uname.value,
      pwd: pwd.value
    }
  })
    .then(function(result) {
      result = JSON.parse(result);
      console.log(result);
      if(result.success) {
        location.replace(result.redirect);
      } else {
        document.getElementById("feedback").innerText = result.error;
      }
    });
}

//gets user from $_SESSION
function getUID() {
  request({
    method: "POST",
    url: "processor.php",
    params: {
      request: "getUID"
    }
  })
    .then(function(result) {
      console.log(result);
    });
}

function checkUserPerms(conlang_id) {
  conlang_id = Number(conlang_id);
  request({
    method: "POST",
    url: "processor.php",
    params: {
      request: "checkUserPerms",
      conlang_id: conlang_id
    }
  })
    .then(function(result) {
      console.log(result);
    });
}

function loadScript(script_id) { //MUST BE CALLED WHEN SCRIPT IS USED
  sheet = document.createElement("style");
  sheet.innerHTML += `@font-face { font-family: "f${script_id}"; src: url("fonts/${script_id}.ttf"); }`
  document.body.appendChild(sheet);
}

function checkMobile() {
  mobile = window.matchMedia("(max-width: 800px)").matches
}

window.addEventListener("resize", checkMobile());
checkMobile();
