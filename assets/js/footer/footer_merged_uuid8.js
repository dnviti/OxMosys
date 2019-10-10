
/**
 * Source Folder: "assets/js/footer/" 
 * Source File Name: "0-slideout.js"
 */

/* Slideout Object */
var slideout = new Slideout({
    "panel": document.getElementById("panel"),
    "menu": document.getElementById("menu"),
    "padding": 256,
    "tolerance": 70
});

slideout.on("translatestart", function () {
    $(".toggle-menu").addClass("is-active");
});

slideout.on("beforeclose", function () {
    $(".toggle-menu").removeClass("is-active");
});

document.querySelector(".toggle-menu").addEventListener("click", function () {
    slideout.toggle();
    if (slideout.isOpen()) {
        $(".toggle-menu").addClass("is-active");
    } else {
        $(".toggle-menu").removeClass("is-active");
    }
});

/*slideout.open();*/

/* ------ */
/**
 * Source Folder: "assets/js/footer/" 
 * Source File Name: "10-autocomplete.js"
 */

// $(document).ready(function () {

//     $("input[type=autocomplete]").each(function (index) {
//         autocomplete(this);
//     });

// });

$("input[type=autocomplete]").focus(function () {
    $(this).off("blur");
    autocomplete(this);
});

$("input[type=autocomplete]").blur(function () {
    $(this).off("focus");
});

function autocomplete(inp) {

    var column = $(inp).attr("name").toUpperCase().replace("-", ".");
    var table = $(inp).attr("table").toUpperCase();
    var value = $(inp).val().toUpperCase();
    var width = $(inp).width() + 26;

    var sql = `
                    SELECT DISTINCT ${column}
                    FROM ${table}
                    WHERE ${column} LIKE '${value}%'
                    ORDER BY ${column} ASC
                `;

    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    $(inp).on("input click", function (e) {
        if ($(inp).val().length > 0) {
            $(inp).css("cursor", "wait");
        }
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) {
            return false;
        }
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        $(a).width(width);
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        getQueryValueAsync(sql, function (json) {
            var arr = [];
            json.forEach(element => {
                column = column.split(".")[column.split(".").length - 1];
                arr.push(element[column.split(".")].toUpperCase());
            });
            // autocomplete(thisElem, json[0]);
            // thisElem.val(json[0][column]);
            for (i = 0; i < arr.length; i++) {
                /*check if the item starts with the same letters as the text field value:*/
                if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                    /*create a DIV element for each matching element:*/
                    b = document.createElement("DIV");
                    /*make the matching letters bold:*/
                    b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                    b.innerHTML += arr[i].substr(val.length);
                    /*insert a input field that will hold the current array item's value:*/
                    b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                    /*execute a function when someone clicks on the item value (DIV element):*/
                    b.addEventListener("click", function (e) {
                        /*insert the value for the autocomplete text field:*/
                        $(inp).val(this.getElementsByTagName("input")[0].value);
                        /*close the list of autocompleted values,
                        (or any other open lists of autocompleted values:*/
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            }
            $(inp).css("cursor", "")
        });

    });
    /*execute a function presses a key on the keyboard:*/
    $(inp).on("keydown", function (e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 38) { //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            if (currentFocus > -1) {
                /*and simulate a click on the "active" item:*/
                if (x) x[currentFocus].click();
            }
        }
    });

    function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
    }

    function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }

    function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}