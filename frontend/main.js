window.onload = () => {

    //ajax with form
    $( "#createURL" ).submit(function (e) {

        e.preventDefault();

        let copy = document.getElementsByClassName('copy')[0];
        copy.innerHTML="";

        if ($( this ).data( 'formstatus' ) !== ' submitting ' ){

            let form = $( this ),
                formUrl = form.attr( 'action' ),
                formMethod = form.attr( 'method' );

            let formData = form.serializeArray();
            let data = {};
            $(formData).each(function(index, obj){
                data[obj.name] = obj.value;
            });

            if (data.longURL != '') {
                $.ajax({

                    url : formUrl,
                    type : formMethod,
                    data : formData,
                    success : function (data) {
                        try {
                            new URL(data);

                            let check = document.getElementsByClassName('shortUrl')[0];
                            if (check) {
                                check.value = data;
                            } else {
                                let gridContent = document.getElementsByClassName('grid__content')[0];
                                let shortUrl = document.createElement('input');
                                shortUrl.classList.add('shortUrl');
                                shortUrl.type = "text";
                                shortUrl.value = data;
                                shortUrl.addEventListener('click', () => {
                                    shortUrl.select();
                                    document.execCommand("copy");
                                    copy.innerHTML = "Copied!";
                                });
                                gridContent.append(shortUrl);
                            }
                        } catch (_) {
                            let check = document.getElementsByClassName('shortUrl')[0];
                            if (check) {
                                check.value = "Wrong URL. Try again";
                            } else {
                                let gridContent = document.getElementsByClassName('grid__content')[0];
                                let shortUrl = document.createElement('input');
                                shortUrl.classList.add('shortUrl');
                                shortUrl.type = "text";
                                shortUrl.value = "Wrong URL. Try again";
                                gridContent.append(shortUrl);
                            }
                        }
                    }

                });
            }
        }
        document.getElementsByClassName('longURL')[0].value = '';
        return false;
    });
};
let button = document.getElementsByName('submit')[0];
button.addEventListener('click', () => {
    let con = document.getElementsByClassName('container')[0];
    con.classList.add('submit__active');
});
