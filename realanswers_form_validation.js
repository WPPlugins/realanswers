//JavaScript for Question Form functionality
//trigger by new question form location value onmouseover event
function real_empty_this(){
    var location_name = document.getElementById('location_name');
    if ((location_name.value == 'What Metro?') || (location_name.value == 'What City?') || (location_name.value == 'What Zipcode?') || (location_name.value == 'What Address?')) {
location_name.value = '';
        };

}
//trigger by new question form notify checkbox onclick event
function show_realanswers_particulars() {
    var notify = document.getElementById('notify_me');
    if (notify.checked) {
        document.getElementById('realanswers_particulars').style.display = 'block';
    } else {
        document.getElementById('realanswers_particulars').style.display = 'none';
    }
}