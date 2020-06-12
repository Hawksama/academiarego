<?php
if ( !defined( 'ABSPATH' ) ) exit;
?>

<div class="group-meet">
<?php
    $user = wp_get_current_user();

    buddymeet_render_jitsi_meet(); 
    
    if ( !in_array( 'stm_lms_instructor', (array) $user->roles ) ) {
        ?>
        <script>
            (function($){
    
                $( document ).ready(function(){
                    jQuery("#jitsiConferenceFrame0").hide();

                    setTimeout(function(){
                        if(api.getNumberOfParticipants() == 1) {
                            api.dispose();
                            $('.group-meet').append('<p class="error" style="color: red; font-weight: bold;"><?= __('You cannot create a room.') ?></p>');
                        } else {
                            jQuery("#jitsiConferenceFrame0").show();
                        }
                    }, 3000);
                });

            })(jQuery);
        </script>
        <?php
    }
?>
</div>