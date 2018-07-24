<?php echo $header;?>
<?php echo $column_left;?>

<div id="content">
 <div id="overlay" style="display:none"><div id="loader"><img src="view/image/smsbump/loader.gif"></div></div>
    <div class="page-header">
        <div class="container-fluid">
          <h1><?php echo $heading_title; ?></h1>
          <ul class="breadcrumb">
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
            <?php } ?>
          </ul>
        </div>
    </div>
    <div class="container-fluid">
    	<?php if ($error_warning) { ?>
            <div class="alert alert-danger autoSlideUp"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
             <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success autoSlideUp"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <script>$('.autoSlideUp').delay(3000).fadeOut(600, function(){ $(this).show().css({'visibility':'hidden'}); }).slideUp(600);</script>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"> <img src="view/template/<?php echo $module_path ?>/smsbumplogosmall.png" /> <span><?php echo $heading_title; ?></span></h3>&nbsp;&nbsp;<i class="fa fa-spinner fa-spin"></i><span id="status"></span>
                <div class="storeSwitcherWidget pull-right">
                	<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><?php echo $store['name']; if($store['store_id'] == 0) echo $text_default; ?>&nbsp;<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                	<ul class="dropdown-menu" role="menu">
                    	<?php foreach ($stores  as $st) { ?>
                    		<li><a href="index.php?route=<?php echo $module_path ?>&store_id=<?php echo $st['store_id'];?>&token=<?php echo $token; ?>"><?php echo $st['name']; ?></a></li>
                    	<?php } ?>
                	</ul>
                </div>
            </div>
            <div class="panel-body">
                <?php if(!empty($data['SMSBump']['APIKey']) ) { ?>
                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                        <input type="hidden" name="store_id" value="<?php echo $store['store_id']; ?>" />
                        <div class="tabbable">
                            <div class="tab-navigation form-inline">
                                <ul class="nav nav-tabs mainMenuTabs" id="mainTabs">
                                    <li><a href="#app_info" data-toggle="tab">General</a></li>
                                    <li><a href="#bulksmssend" data-toggle="tab">Bulk Messaging</a></li>
                                    <li><a href="#actions" data-toggle="tab">Transactional SMS</a></li>
                                    <li><a href="#main_settings" data-toggle="tab">Settings</a></li>
                                    <li><a href="#support" data-toggle="tab">Support</a></li>
                                </ul>
                                <div class="tab-buttons">
                                    <button type="submit" class="btn btn-success save-changes"><i class="fa fa-check"></i>&nbsp;<?php echo $save_changes?></button>
                                    <a onclick="location = '<?php echo $cancel; ?>'" class="btn btn-warning"><i class="fa fa-times"></i>&nbsp;<?php echo $button_cancel?></a>
                                </div>
                            </div><!-- /.tab-navigation -->
                            <div class="tab-content">
                                <div id="bulksmssend" class="tab-pane fade"><?php require_once(DIR_APPLICATION.'view/template/'.$module_path.'/tab_bulksmssend.php'); ?></div>
                                <div id="actions" class="tab-pane fade"><?php require_once(DIR_APPLICATION.'view/template/'.$module_path.'/tab_actions.php'); ?></div>
                          	    <div id="main_settings" class="tab-pane fade"><?php require_once(DIR_APPLICATION.'view/template/'.$module_path.'/tab_settings.php'); ?></div>
                                <div id="support" class="tab-pane fade"><?php require_once(DIR_APPLICATION.'view/template/'.$module_path.'/tab_support.php'); ?></div>
                                <div id="app_info" class="tab-pane fade"><?php require_once(DIR_APPLICATION.'view/template/'.$module_path.'/tab_app.php'); ?></div>
                            </div> <!-- /.tab-content -->
                        </div><!-- /.tabbable -->
                    </form>
                <?php } else { ?>
                    <form class="form-default" id="login-form" method="post">
                        <div class="login_form">
                            <h3>Welcome</h3>
                            <h2>Enter email address and phone number to start using SMSBump</h2>
                             <div class="alert alert-danger autoSlideUp" id="response_error">
                                <span id="error_message"></span>
                               <span data-toggle="tooltip" data-placement="bottom" title="Use this option in case you have entered wrong phone number during your initial registration."><a onclick="resetAccount()" href="javascript:void(0)">Reset account and start over</a></span>
                            </div>
                            <input name="login_email" type="email" class="form-control" placeholder="Email address">
                            <select name="country_code" class="form-control" >
                                 <?php foreach ($countries as $country) { ?>
                                    <option data-country-code="<?php echo strtolower($country['d_code']); ?>" value="<?php echo strtolower($country['code']); ?>" <?php echo (!empty($country['code']) && $country['code'] == 'US') ? 'selected=selected' : '' ?>><?php echo $country['name'] ?> </option>
                                  <?php } ?>
                            </select>
                             <input type="hidden" id="country_label" value="us" />
                             <div class="input-group">
                                    <span class="input-group-addon login_country_code">+1</span>
                                    <input name="login_country_code" type="hidden" value="+1"/>
                                    <input name="login_phone" type="text" class="form-control" placeholder="6176006661"/>
                            </div>
                            <div class="e-submit">
                                <button type="submit" class="btn btn-primary" id="login-form-submit"  value="Log in">Verify my number</button>
                            </div>
                            <span data-toggle="tooltip" data-placement="bottom" title="Use this option in case you have entered wrong phone number during your initial registration."><a onclick="resetAccount()" class="reset_" href="javascript:void(0)">Reset account and start over</a></span>
                            <input type="hidden" name="store_id" value="<?php echo $store['store_id']; ?>" />
                        </div>
                    </form>
                    <form class="form-default" id="confirm-form" method="post">
                        <div class="login_form">
                            <h3>Confirm</h3>
                              <div id="verification_code" class="alert alert-success">
                                <span style="font-weight:600; font-size:13px;"></span>
                              </div>
                            <input name="confirm_code" type="text" class="form-control" placeholder="Verification code"/>
                            <div class="e e-submit">
                                <button type="submit" class="btn btn-primary" id="confirm-form-submit" value="Confirm">Confirm my account</button>
                            </div>
                            <div class="e-submit" >
                               <button onclick="location.reload()" class="btn btn-default">Back</button>
                            </div>
                        </div>
                        <input name="store_id" type="hidden" class="form-control" value="<?php echo(!empty($store_id) ? $store_id : 0) ?>">
                        <input name="login_email" type="hidden" class="form-control" value="<?php echo(!empty($email) ? $email : '') ?>">
                        <input name="login_phone" type="hidden" class="form-control" value="<?php echo(!empty($phone) ? $phone : '') ?>">
                    </form>
                    <!-- confirm phone number modal -->
                    <div class="modal fade" id="confirm_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
                      <div class="modal-dialog " role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <img src="view/template/extension/module/smsbump/smsbumplogo.png"  alt="SMSBump">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          </div>
                          <div class="modal-body">
                            <h4 class="modal-title"></h4>
                          </div>
                          <div class="modal-footer">
                            <button id="confirm_button" type="button" class="btn btn-success">Yes</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- reset account modal -->
                    <div class="modal fade" id="reset_account" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
                      <div class="modal-dialog " role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <img src="view/template/extension/module/smsbump/smsbumplogo.png"  alt="SMSBump">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          </div>
                          <div class="modal-body">
                            <form>
                              <div class="form-group">
                                <div class="alert alert-danger autoSlideUp" id="reset_email_error">
                                    <span id="wrong_email"></span>
                                </div>
                                <label for="recipient-name" class="control-label">Please enter your email address</label>
                                <input type="text" class="form-control" id="reset_email">
                              </div>
                            </form>
                          </div>
                          <div class="modal-footer">
                            <button id="reset_button" type="button" class="btn btn-primary">Reset account</button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <script>
                        
                      
                        function validateLogin() {
                            var error = "";
                            if($('[name="login_email"]').val().length < 1 || $('[name="login_phone"]').val().length < 1) {
                                error = "All fields must be filled!";
                                return error;
                            }

                            return error;
                        }
                      
                       $('#login-form-submit').on('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            var country_code = $('[name="login_country_code"]').val();
                            var country_label = $('#country_label').val();
                            $('#response_error').slideUp("slow");
                            var validate = validateLogin();
                            if (validate.length < 1) {
                                 $('#confirm_modal').modal('show')
                                 $('#confirm_modal').on('shown.bs.modal', function () {
                                  var number = $('[name="login_phone"]').val();
                                  var modal = $(this);
                                  modal.find('.modal-title').html('Are you sure that <strong>' + country_code +' '+ number + '</strong> is the correct number?')
                                })

                                $('#confirm_button').on('click', function(){

                                        $('#overlay').css('display','block');
                                        $('#overlay').toggleClass('active');

                                        $('#confirm_modal').modal('hide')

                                        var customer_phone = $('[name="login_country_code"]').val() + $('[name="login_phone"]').val();

                                        $.ajax({
                                        url: '<?php echo htmlspecialchars_decode("https://api.smsbump.com/userlogon/1f8DSYextlR1.json") ?>',
                                        type: 'GET',
                                        data: { email: $('[name="login_email"]').val(),
                                                phone: customer_phone,
                                              },

                                            success: function (response) {
                                            $('#overlay').css('display','none');
                                            $('#overlay').toggleClass('active'); 
                                            if(response.status == "success" && !response.data.user) {
                                               $('#verification_code span').html(response.data.message);
                                               $('#login-form').slideUp();
                                               $('#confirm-form').slideDown();
                                               $('#confirm-form-submit').on('click', function (event) {
                                                    $('#overlay').css('display','block');
                                                    $('#overlay').toggleClass('active');
                                                    event.preventDefault();
                                                    event.stopPropagation();
                                                    $.ajax({
                                                        url: '<?php echo htmlspecialchars_decode("https://api.smsbump.com/userlogon/1f8DSYextlR1.json") ?>',
                                                        type: 'GET',
                                                        data: { //store_id: $('[name="store_id"]').val(),
                                                                email: $('[name="login_email"]').val(),
                                                                phone: customer_phone,
                                                                code:  $('[name="confirm_code"]').val()
                                                              },
                                                        success: function(result) {

                                                            if(result.status == "success" && result.data.user.apps[0].apikey) {
                                                                
                                                                $.ajax({
                                                                    url: '<?php echo htmlspecialchars_decode($saveApiKey) ?>',
                                                                    type: 'GET',
                                                                    data: { store_id: $('[name="store_id"]').val(),
                                                                            api_key:  result.data.user.apps[0].apikey,
                                                                            api_secret:  result.data.user.apps[0].apisecret,
                                                                            register_country_prefix:country_code,
                                                                            register_country_label:country_label,
                                                                    },
                                                                    dataType: "json",
                                                                    success: function (addResult) {
                                                                        if(addResult.status == "success"){
                                                                            location.reload(addResult.redirect_url);
                                                                        } else {
                                                                            $('#overlay').css('display','none');
                                                                            $('#overlay').toggleClass('active');
                                                                            alert("Unknown error!");
                                                                        }  
                                                                    }
                                                                });
                                                            } else if(result.status == "error") {
                                                                $('#overlay').css('display','none');
                                                                $('#overlay').toggleClass('active');
                                                                alert(result.data.message);
                                                            }
                                                        }
                                                    });
                                               });
                                            } else if(response.status == "success" && response.data.user.apps[0].apikey) {
                                     
                                                $.ajax({
                                                    url: '<?php echo htmlspecialchars_decode($saveApiKey) ?>',
                                                    type: 'GET',
                                                    data: { store_id: $('[name="store_id"]').val(),
                                                            api_key:  response.data.user.apps[0].apikey,
                                                            api_secret:  response.data.user.apps[0].apisecret,
                                                            register_country_prefix:country_code,
                                                            register_country_label:country_label,
                                                    },
                                                    dataType: "json",
                                                    success: function (addResult) {

                                                        if(addResult.status == "success")
                                                            location.reload(addResult.redirect_url);
                                                        else
                                                            alert("Unknown error!");
                                                    }
                                                });
                                              } else if(response.status == "error") {
                                                //alert(response.data.message);
                                                $('#response_error #error_message').html(response.data.message);
                                                $('#response_error').slideDown("slow");
                                                //$('#response_error').slideDown("slow").delay(5000).slideUp("slow");

                                            }
                                        }
                                    });
                                });
                            } else {
                                alert(validate);
                            }
                        });

                    function validateEmail(email){
                        var pattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                        if (pattern.test(email)) {
                            return (true)
                        }
                        return (false)
                    };
                    function resetAccount () {

                         $('#reset_account').modal('show');
                         $('#reset_account').on('shown.bs.modal', function () {

                            $('#reset_button').click(function(e) {
                                 $('#reset_email_error').slideUp("slow");
                                e.preventDefault();
                                e.stopPropagation();

                                if ( $('#reset_email').val().length > 1 && validateEmail($('#reset_email').val()))  {

                                    $.ajax({
                                        url: '<?php echo htmlspecialchars_decode("https://api.smsbump.com/userlogon/1f8DSYextlR1.json") ?>',
                                        type: 'GET',
                                        data: { email: $('#reset_email').val(),
                                                phone: '',
                                                reset: true
                                        },
                                        dataType: "json",
                                        success: function(response){
                                            if (response.status == 'error') {
                                                alert(response.data.message);
                                            } else {
                                                alert(response.data.message);
                                                 $('#reset_account').modal('hide');
                                            }
                                        }
                                    });
                                } else {
                                    $('#reset_email_error #wrong_email').html('Please enter a valid email address');
                                    $('#reset_email_error').slideDown("slow");
                                }
                            });
                        });
                    }

                    </script>
                <?php } ?>
    			<div class="box-heading" style="text-align:center">
                    <h5>This service is provided by <a href="http://smsbump.com" target="_blank"><img src="view/template/<?php echo $module_path ?>/smsbumplogo.png" style="max-height:19px;" /></a></h5>
                </div>

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
openSMSBumpURL = function(verb, url, data, target) {
        var form = document.createElement("form");
        form.action = url;
        form.method = verb;
        form.target = target || "_self";
        if (data) {
          for (var key in data) {
            var input = document.createElement("textarea");
            input.name = key;
            input.value = typeof data[key] === "object" ? JSON.stringify(data[key]) : data[key];
            form.appendChild(input);
          }
        }
        form.style.display = 'none';
        document.body.appendChild(form);
        form.submit();
      };


$(function() {
    $('.mainMenuTabs a:first').tab('show'); // Select first tab
     $('.mainMenuTabs a:first').click();
    if (window.localStorage && window.localStorage['currentTab']) {
        $('.mainMenuTabs a[href="'+window.localStorage['currentTab']+'"]').tab('show');
    }
    if (window.localStorage && window.localStorage['currentSubTab']) {
        $('a[href="'+window.localStorage['currentSubTab']+'"]').tab('show');
    }
    $('.fadeInOnLoad').css('visibility','visible');
    $('.mainMenuTabs a[data-toggle="tab"]').click(function() {
        if (window.localStorage) {
            window.localStorage['currentTab'] = $(this).attr('href');
        }
    });
    $('a[data-toggle="tab"]:not(.mainMenuTabs a[data-toggle="tab"], .app_info a[data-toggle="tab"])').click(function() {
        if (window.localStorage) {
            window.localStorage['currentSubTab'] = $(this).attr('href');
        }
    });
});
function enterAPIKey() {
    $('a[href=#tab_app]').trigger('click');
    $('#APIKey').attr('autofocus','autofocus');
}


</script>
<script type="text/javascript">
        function formatState (state) {
            if (!state.id) { return state.text; }

          var $state = $(
            '<span><img src="view/image/smsbump/country_flags/' + state.element.value.toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span><span style="float:right;" class="dial_code">('+state.element.getAttribute('data-country-code').toLowerCase()+')</span>'
          );

          return $state;
        };

        $(document).ready(function() {
            $("select[name='country_code']").select2({
              templateResult: formatState,
              templateSelection: formatState
            });

            $("select[name='country_code']").on("select2:select", function(e) {
                $('.login_country_code').html(e.params.data.element.getAttribute('data-country-code'));
                $('input[name="login_country_code"]').val(e.params.data.element.getAttribute('data-country-code'));
                $('#country_label').val(e.params.data.element.value);
            });
        });
    </script>
   <?php if($status) { ?>
   <script>
   $('.fa-spinner').css('display','inline-block');
   </script>
   <?php } else { ?>
     <script>
     $('.fa-spinner').css('display','none');
   </script>
   <script>
     $(window).load(function(){
        $('#app_info').parent().click();
     });
   </script>
   <?php } ?>
<?php echo $footer; ?>
