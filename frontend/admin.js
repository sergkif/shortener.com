//ajax with event (not form)
function deleteURL(id) {
    $.ajax({

        url: "admin.php",
        type: "POST",
        data: {id: id},
        success: function(data){
            if(data == '200'){
                let tr = document.getElementById(id);
                tr.parentNode.removeChild(tr);
            } else {
                alert("Something wrong");
            }
        }
    });
}

function adminExit() {
    $.ajax({

        url: "admin.php",
        type: "POST",
        data: {exit: true},
        success: function(data){
            if(data == '200'){
                location.href = "admin.php";
            } else {
                alert("Something wrong");
            }
        }
    });
}

// let button = document.getElementsByName('submit')[0];
// if(button){
//     button.addEventListener('click', () => {
//         let con = document.getElementsByClassName('container')[0];
//         con.classList.add('submit__active');
//     });
// }
