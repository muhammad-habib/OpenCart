<table class="table"> 
    <tr>
        <td class="col-xs-3">
            <h5><strong>Transactional Messages Sender:</strong></h5>
            <span class="help"><i class="fa fa-info-circle"></i>&nbsp;This field will be taken into account only if you are subscribed to our <a href="http://smsbump.com/pages/pricing">Priority Plan</a> or you have purchased a <a id="dedicated_number_transactional" href="javascript:void(0)">Dedicated Phone Number</a> .<br /><br />- Latin characters are supported only.</span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <div class="form-group" style="padding-top:10px;">
                    <input type="text" class="form-control" name="SMSBump[From]" value="<?php if(isset($data['SMSBump']['From'])) { echo $data['SMSBump']['From']; } else { echo "SMSBump"; }?>" />
                </div>
                <select class="form-control" id="DedicatedNumbersTransactional">
                    <?php foreach ($dedicated_numbers as $number) { ?>
                        <option value="<?php echo $number; ?>"><?php echo $number; ?></option>
                     <?php } ?>
                </select>
                <br />    
                <?php if($dedicated_numbers) { ?>
                   <div>
                        <div class="col-xs-12 clearPadding">
                            <label ><input type="checkbox"  name="SMSBump[UseDedicatedNumberForTransactional]" <?php echo (!empty($data['SMSBump']['UseDedicatedNumberForTransactional']) && $data['SMSBump']['UseDedicatedNumberForTransactional'] == 'on') ? 'checked=checked' : '' ?>  id="useDedicatedNumbersForTransactional" >Use one of your dedicated numbers.</label>
                        </div>
                   </div>
                <?php } ?>
                <input type="hidden" class="form-control" name="SMSBump[SelectedDedicatedNumber]" value="<?php if(isset($data['SMSBump']['SelectedDedicatedNumber'])) { echo $data['SMSBump']['SelectedDedicatedNumber']; } else { echo ""; }?>" />
            </div>
        </td>
    </tr>  
    <tr>
        <td class="col-xs-3">
            <h5><strong>Send short messages only to specific country:</strong></h5>
            <span class="help"><i class="fa fa-info-circle"></i>&nbsp;Enable this option if you want to send short messages only to specific country. <i>Note: Phone numbers which are not from the selected country will be omitted!</i></span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <select name="SMSBump[PhoneNumberPrefix]" id="Check" class="form-control">
                      <option value="yes" <?php echo (!empty($data['SMSBump']['PhoneNumberPrefix']) && $data['SMSBump']['PhoneNumberPrefix'] == 'yes') ? 'selected=selected' : '' ?>><?php echo $text_enabled; ?></option>
                      <option value="no"  <?php echo (empty($data['SMSBump']['PhoneNumberPrefix']) || $data['SMSBump']['PhoneNumberPrefix']== 'no') ? 'selected=selected' : '' ?>><?php echo $text_disabled; ?></option>
                </select>
                <div class="prefix">
                    <select name="select_number_prefix" class="form-control" >
                        <?php foreach ($countries as $country) { ?>
                            <option data-country-code="<?php echo strtolower($country['d_code']); ?>" value="<?php echo strtolower($country['code']); ?>" <?php echo (!empty($country['code']) && $country['code'] == 'US') ? 'selected=selected' : '' ?>><?php echo $country['name'] ?> </option>
                        <?php } ?>
                    </select>
                    <input type="hidden" class="form-control" name="SMSBump[NumberPrefix]" value="<?php if(isset($data['SMSBump']['NumberPrefix'])) { echo $data['SMSBump']['NumberPrefix']; } else { echo ""; }?>" />
                     <input type="hidden" class="form-control" name="SMSBump[CountryCode]" value="<?php if(isset($data['SMSBump']['CountryCode'])) { echo $data['SMSBump']['CountryCode']; } else { echo ""; }?>" />
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="col-xs-3">
            <h5><strong>Store owner phone numbers:</strong></h5>
            <span class="help"><i class="fa fa-info-circle"></i>&nbsp;The added phone numbers will be used for admin notifications.</span>
        </td>
        <td class="col-xs-9" id="storeOwnerInputs">   
            <div class="col-xs-4">
               <div id="showHideOption">
                    <div class="col-xs-12 clearPadding">
                        <label ><input type="checkbox"  id="addCountryCodeToStoreOwnerNumber" >Uncheck this option, if you want to change the country code.</label>
                    </div>
                </div>

                <div class="col-xs-2 clearPadding code">
                    <input type="text" class="form-control" id="input-store_owner_country_code" disabled="disabled" value="<?php if(isset($data['SMSBump']['NumberPrefix'])) { echo $data['SMSBump']['NumberPrefix']; } else { echo ""; }?>" />
                </div>
                <div class="col-xs-10 clearPadding number">
                    <div class="input-group">
                        <input type="text" class="form-control" id="input-store_owner_phone" value="" />
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button" id="addStoreOwner">Add</button>
                        </span>
                    </div>
                </div>
            </div>
        </td>
    </tr>

    <tr>
        <td class="col-xs-3">
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <div id="storeOwnerTelephone" class="scrollbox form-control">
                    <?php if(!empty($data['SMSBump']['StoreOwnerPhoneNumber'])) { ?>
                        <?php $i = 0 ?>
                        <?php foreach($data['SMSBump']['StoreOwnerPhoneNumber'] as $store_owner_number) { ?>
                            <div id="storeOwnerTelephone<?php echo $i ?>"><i class="fa fa-minus-circle"></i>&nbsp;<?php echo $store_owner_number ?><input type="hidden" name="SMSBump[StoreOwnerPhoneNumber][]" value="<?php echo $store_owner_number ?>" /></div>                            
                            <?php $i++; ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </td>
    </tr>
</table>
<script type="text/javascript">
// Display & Hide the settings

$(function() {
    var $checkbox = $('#useDedicatedNumbersForTransactional');
    var $selectDedicated = $('#DedicatedNumbersTransactional');
    var $inputFrom = $('input[name="SMSBump[From]"]');
     if ($checkbox.prop('checked') === true) {
            $selectDedicated.show();
            $inputFrom.hide(); 
        }
        else {
            $selectDedicated.hide(); 
            $inputFrom.show(); 
        }
    $checkbox.change(function(){
        if ($checkbox.prop('checked') === true) {
            $selectDedicated.show(300); 
            $inputFrom.hide(300);
        }
        else {
            $selectDedicated.hide(300);
            $inputFrom.show(300); 
        }
    }); 
});


$(function() {
    var $typeSelector = $('#Check');
    var $toggleArea = $('.prefix');
     if ($typeSelector.val() === 'yes') {
            $toggleArea.show(); 
        }
        else {
            $toggleArea.hide(); 
        }
    $typeSelector.change(function(){
        if ($typeSelector.val() === 'yes') {
            $toggleArea.show(300); 
        }
        else {
            $toggleArea.hide(300); 
        }
    }); 
});
$(function() {
    var $typeSelector = $('#CheckPrefix');
    var $toggleArea = $('.strict-prefix');
     if ($typeSelector.val() === 'yes') {
            $toggleArea.show(); 
        }
        else {
            $toggleArea.hide(); 
        }
    $typeSelector.change(function(){
        if ($typeSelector.val() === 'yes') {
            $toggleArea.show(300); 
        }
        else {
            $toggleArea.hide(300); 
        }
    }); 
});
</script>

<script type="text/javascript">
        $("#DedicatedNumbersTransactional").on('change', function (e) {
            var optionSelected = $("option:selected", this);
            var valueSelected = this.value;
            $('input[name="SMSBump[SelectedDedicatedNumber]"]').val(valueSelected);
        });
        
        var selected_dedicated_number = "<?php echo (!empty($data['SMSBump']['SelectedDedicatedNumber']) ? $data['SMSBump']['SelectedDedicatedNumber'] : '') ?>";
        
        if(selected_dedicated_number != '') {
            $("#DedicatedNumbersTransactional").val(selected_dedicated_number);
        } else {
            $("#DedicatedNumbersTransactional").val($("#DedicatedNumbersTransactional option:first").val());
        }

        



        function formatStatePrefix (state) {
            if (!state.id) { return state.text; }

          var $state = $(
            '<span><img src="view/image/smsbump/country_flags/' + state.element.value.toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span><span style="float:right;" class="dial_code">('+state.element.getAttribute('data-country-code').toLowerCase()+')</span>'
          );

          return $state;
        };

        $(document).ready(function() {
            $("select[name='select_number_prefix']").select2({
              templateResult: formatStatePrefix,
              templateSelection: formatStatePrefix
            });

            $("select[name='select_number_prefix']").on("select2:select", function(e) {
                $('input[name="SMSBump[NumberPrefix]"]').val(e.params.data.element.getAttribute('data-country-code'));
                $('input[name="SMSBump[CountryCode]"]').val(e.params.data.element.value);
            });
            var selected_country = "<?php echo $selected_country ?>";
            $("select[name='select_number_prefix']").select2('val', selected_country);

        });
</script>
<script>
    var owner_number = <?php echo(!empty($data['SMSBump']['StoreOwnerPhoneNumber']) ? count($data['SMSBump']['StoreOwnerPhoneNumber']) : 0) ?>;

    $("#addStoreOwner").click(function(e) {
        e.preventDefault();
        e.stopPropagation();

        if ($('input[id=\'input-store_owner_country_code\']').val() && $('input[id=\'input-store_owner_phone\']').val()) {
            var full_phone_number = $('input[id=\'input-store_owner_country_code\']').val() + $('input[id=\'input-store_owner_phone\']').val();
            
            $('#storeOwnerTelephone').append('<div id="storeOwnerTelephone' + owner_number + '">' + '<i class="fa fa-minus-circle"></i>&nbsp;' + full_phone_number + '<input type="hidden" name="SMSBump[StoreOwnerPhoneNumber][]" value="' + full_phone_number + '" /></div>');
            owner_number++;
            $('#storeOwnerTelephone div:odd').attr('class', 'odd');
            $('#storeOwnerTelephone div:even').attr('class', 'even');
    
            $('input[id=\'input-store_owner_phone\']').val('');
        } else {
            alert('Error: All fileds are required!');
        }
    });

    $('#storeOwnerTelephone').delegate('.fa-minus-circle', 'click', function() {
        $(this).parent().remove();

        $('#storeOwnerTelephone div:odd').attr('class', 'odd');
        $('#storeOwnerTelephone div:even').attr('class', 'even'); 
    });

    //Store owner number formating
   
    $(function() {
        var $typeSelector = $('#Check');
        var $toggleArea = $('#addCountryCodeToStoreOwnerNumber');
        var $showHideOption = $('#showHideOption');
        var $toogleInput = $('#input-store_owner_country_code');
        if ($typeSelector.val() === 'yes') {
            $showHideOption.show();            
            $toggleArea.prop('checked', true); 
            $toogleInput.prop('disabled', true); 
        } else {
            $showHideOption.hide();
            $toggleArea.prop('checked', false);
            $toogleInput.prop('disabled', false);  
            $('#input-store_owner_country_code').val('+1')
            $('input[name="SMSBump[NumberPrefix]"]').val('');
            $('input[name="SMSBump[CountryCode]"]').val('');
        }
        $typeSelector.change(function(){
            if ($typeSelector.val() === 'yes') {
                $showHideOption.show(300);
               $toggleArea.prop('checked', true); 
               $toogleInput.prop('disabled', true);
            $('#input-store_owner_country_code').val($("select[name='select_number_prefix'] option:selected").attr('data-country-code'));
            $('input[name="SMSBump[NumberPrefix]"]').val('+1');
            $('input[name="SMSBump[CountryCode]"]').val('us');
            
            }
            else {
                 $showHideOption.hide(300);
                $toggleArea.prop('checked', false);
                $toogleInput.prop('disabled', false); 
                $('#input-store_owner_country_code').val('+1')
                $('input[name="SMSBump[NumberPrefix]"]').val('');
                $('input[name="SMSBump[CountryCode]"]').val('');
            }
        }); 
    });

    $(function() {
        var $typeSelector = $('#addCountryCodeToStoreOwnerNumber');
        var $toggleArea = $('#input-store_owner_country_code');
         if ($typeSelector.prop('checked') === true) {
                $toggleArea.prop('disabled', true);
                $('#input-store_owner_country_code').val($("select[name='select_number_prefix'] option:selected").attr('data-country-code'));
            }
            else {
                $toggleArea.prop('disabled', false); 
                $('#input-store_owner_country_code').val('+1')
            }
        $typeSelector.change(function(){
            if ($typeSelector.prop('checked') === true) {
               $toggleArea.prop('disabled', true);
               $('#input-store_owner_country_code').val($("select[name='select_number_prefix'] option:selected").attr('data-country-code')); 
            }
            else {
                $toggleArea.prop('disabled', false);
                $('#input-store_owner_country_code').val('+1') 
            }
        }); 
    });

    $(function() {
        var $typeSelector = $('#addCountryCodeToStoreOwnerNumber');
        var $toggleArea = $('#input-store_owner_country_code');
         if ($typeSelector.prop('checked') === true) {
                $toggleArea.prop('disabled', true);
                $('#input-store_owner_country_code').val($("select[name='select_number_prefix'] option:selected").attr('data-country-code'));
            }
            else {
                $toggleArea.prop('disabled', false); 
                $('#input-store_owner_country_code').val('+1')
            }
        $typeSelector.change(function(){
            if ($typeSelector.prop('checked') === true) {
               $toggleArea.prop('disabled', true);
               $('#input-store_owner_country_code').val($("select[name='select_number_prefix'] option:selected").attr('data-country-code')); 
            }
            else {
                $toggleArea.prop('disabled', false);
                $('#input-store_owner_country_code').val('+1') 
            }
        }); 
    });

    $("select[name='select_number_prefix']").on("select2:select", function(e) {
        $('#input-store_owner_country_code').val(e.params.data.element.getAttribute('data-country-code'));
    }); 

    $('#dedicated_number_transactional').click(function(e) {
            e.preventDefault();
            e.stopPropagation();

            openSMSBumpURL('POST', 'https://smsbump.com/users/login', {api_key: '<?php echo $data['SMSBump']['APIKey']  ?>', api_secret: '<?php echo $data['SMSBump']['APISecret']  ?>', redirect: 'https://smsbump.com/numbers'}, "_blank");


        });
    
</script>

