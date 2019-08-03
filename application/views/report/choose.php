        <?php
            //todo byt namn på fil beskrivande
            echo form_open("reports/index");

            $options = array();
            foreach($staff as $person){
                $options[$person["id"]] = $person["fornamn"];
            }

            echo form_dropdown('person', $options);
            echo form_submit("","Okej");
            echo "</form>";
        ?>
        