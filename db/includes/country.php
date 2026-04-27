<?php
function renderCountrySelect($label = "Destination Country", $name = "delivering_country", $id = "custom-country-1")
{
    include 'constants/country-constants.php'; // import $countries array

    echo '
    <div class="col-md-6">
        <div class="form-group custom-select-container" id="' . $id . '">
            <label class="form-label" for="' . $name . '_hidden">' . $label . '</label>

            <div class="custom-select" role="presentation">
                <button type="button" class="select-toggle" aria-haspopup="listbox" aria-expanded="false" id="select-toggle-' . $id . '">
                    <span class="selected-text">Select Country</span>
                    <span class="arrow">▾</span>
                </button>

                <ul class="options" role="listbox" tabindex="-1" aria-label="Countries" id="options-list-' . $id . '">
                    <li data-value="" class="option">Select Country</li>';

    // Loop through and render country options
    foreach ($countries as $country) {
        echo '<li data-value="' . htmlspecialchars($country) . '" class="option">' . $country . '</li>';
    }

    echo '
                </ul>
                <input type="hidden" name="' . $name . '" id="' . $name . '_hidden" value="">
            </div>
        </div>
    </div>';
}
?>