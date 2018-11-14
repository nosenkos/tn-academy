<div class="modal fade" id="member-form" tabindex="-1" role="dialog" aria-labelledby="member-form"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel"><?php echo __('Registration Form',TNA_PLUGIN_NAME);?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tn_academy-member-form" action="#" method="post"
                      data-url="<?php echo admin_url('admin-ajax.php'); ?>">

                    <div class="field-container form-group">
                        <input type="text" class="field-input form-control" placeholder="<?php echo __('Your First Name',TNA_PLUGIN_NAME);?>" id="name" name="first_name"
                               required>
                        <small class="field-msg error" data-error="invalidFirstName"><?php echo __('Your First Name is Required',TNA_PLUGIN_NAME);?></small>
                    </div>

                    <div class="field-container form-group">
                        <input type="text" class="field-input form-control" placeholder="<?php echo __('Your Last Name',TNA_PLUGIN_NAME);?>" id="last_name"
                               name="last_name" required>
                        <small class="field-msg error" data-error="invalidLastName"><?php echo __('Your Last Name is Required',TNA_PLUGIN_NAME);?></small>
                    </div>

                    <div class="field-container form-group">
                        <input type="text" class="field-input form-control" placeholder="<?php echo __('Your Address',TNA_PLUGIN_NAME);?>" id="address" name="address"
                               required>
                        <small class="field-msg error" data-error="invalidAddress"><?php echo __('Your Address is Required',TNA_PLUGIN_NAME);?></small>
                    </div>

                    <div class="field-container form-group">
                        <input type="text" class="field-input form-control" placeholder="<?php echo __('Your ZIP Code',TNA_PLUGIN_NAME);?>" id="zip" name="zip" required>
                        <small class="field-msg error" data-error="invalidZip"><?php echo __('Your ZIP Code is Required',TNA_PLUGIN_NAME);?></small>
                    </div>

                    <div class="field-container form-group">
                        <input type="text" class="field-input form-control" placeholder="<?php echo __('Your City',TNA_PLUGIN_NAME);?>" id="city" name="city" required>
                        <small class="field-msg error" data-error="invalidCity"><?php echo __('Your City is Required',TNA_PLUGIN_NAME);?></small>
                    </div>

                    <div class="field-container form-group">
                        <input type="email" class="field-input form-control form-control-danger" placeholder="<?php echo __('Your Email',TNA_PLUGIN_NAME);?>" id="email" name="email"
                               required>
                        <small class="field-msg error form-text text-muted" data-error="invalidEmail"><?php echo __('The Email address is not valid',TNA_PLUGIN_NAME);?></small>
                    </div>

                    <div class="field-container form-group">
                        <input type="tel" class="field-input form-control" placeholder="<?php echo __('Your Phone',TNA_PLUGIN_NAME);?>" id="phone" name="phone" required>
                        <small class="field-msg error" data-error="invalidPhone"><?php echo __('Your Phone Number is Required',TNA_PLUGIN_NAME);?></small>
                    </div>

                    <div class="field-container form-group">
                        <textarea name="message" id="message" class="field-input form-control"
                                  placeholder="<?php echo __('What are your current job description?',TNA_PLUGIN_NAME);?>" required></textarea>
                        <small class="field-msg error" data-error="invalidMessage"><?php echo __('A Current Job Description is
                            Required',TNA_PLUGIN_NAME);?>
                        </small>
                    </div>

                    <div class="field-container">
                        <div>
                            <button type="stubmit" class="tn-registration"><?php echo __('Submit',TNA_PLUGIN_NAME);?></button>
                        </div>
                        <small class="field-msg js-form-submission"><?php echo __('Submission in process, please wait',TNA_PLUGIN_NAME);?>&hellip;</small>
                        <small class="field-msg success js-form-success"><?php echo __('Message Successfully submitted, thank you!',TNA_PLUGIN_NAME);?>
                        </small>
                        <small class="field-msg error js-form-error"><?php echo __('There was a problem with the Contact Form, please
                            try again!',TNA_PLUGIN_NAME);?>
                        </small>
                    </div>

                    <input type="hidden" name="action" value="submit_member">
                    <input type="hidden" name="course" value="<?php echo get_the_title(); ?>">
                    <input type="hidden" name="course_id" value="<?php echo get_the_ID(); ?>">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce("member-nonce") ?>">

                </form>
                <p id="js-thanks" class="tn_hide"><?php echo __('Thank you for your application!', TNA_PLUGIN_NAME );?></p>
            </div>
        </div>
    </div>
</div>
