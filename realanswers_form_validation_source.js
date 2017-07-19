//Source JavaScript for Question Form functionality
function real_toggle_location_value() {
    var location_type = document.getElementById('location_type');
    var location_name = document.getElementById('location_name');
    if (location_type.selectedIndex == 0) {
        location_name.value = 'What Metro?';
        location_name.onfocus = function (event) {
            location_name.value = '';
        };
    }
    if (location_type.selectedIndex == 1) {
        location_name.value = 'What City?';
        location_name.onfocus = function (event) {
            location_name.value = '';
        };
    }
    if (location_type.selectedIndex == 2) {
        location_name.value = 'What Zipcode?';
        location_name.onfocus = function (event) {
            location_name.value = '';
        };
    }
    if (location_type.selectedIndex == 3) {
        location_name.value = 'What Address?';
        location_name.onfocus = function (event) {
            location_name.value = '';
        };
    }
}// end of real toggle value
function show_realanswers_particulars() {
    var notify = document.getElementById('notify_me');
    if (notify.checked == true) {
        document.getElementById('realanswers_particulars').style.display = 'block';
    } else {
        document.getElementById('realanswers_particulars').style.display = 'none';
    }
}