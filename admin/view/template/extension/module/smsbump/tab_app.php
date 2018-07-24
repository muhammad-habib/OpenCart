<table class="table">
    <tr>
        <td class="col-xs-3"><h5><strong><span class="required">* </span><?php echo $entry_code; ?></strong></h5></td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <select name="SMSBump[Enabled]" id="module_status" class="form-control">
                      <option value="yes" <?php echo (!empty($data['SMSBump']['Enabled']) && $data['SMSBump']['Enabled'] == 'yes') ? 'selected=selected' : '' ?>><?php echo $text_enabled; ?></option>
                      <option value="no"  <?php echo (empty($data['SMSBump']['Enabled']) || $data['SMSBump']['Enabled']== 'no') ? 'selected=selected' : '' ?>><?php echo $text_disabled; ?></option>
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td class="col-xs-3">
            <h5><strong>Account Balance:</strong></h5>
            <span class="help"><i class="fa fa-info-circle"></i>&nbsp;The field displays your current account balance. You can load funds at any time by pressing on the <i class="fa fa-plus"></i> sign. <a href="http://smsbump.com/pages/pricing">View country pricing.</a></span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <div class="btn-group">
                  <button type="button" class="btn btn-success text"><span id="balance">0.00 USD</span></button>
                  <button type="button" class="btn btn-success balance-plus dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="addFundsDropdown">
                        <span class="fa fa-plus" aria-hidden="true"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu" aria-labelledby="addFundsDropdown">
                    <li role="amount"><a role="menuitem" tabindex="-1" onClick="addFunds('20.00');">$ 20.00</a></li>
                    <li role="amount"><a role="menuitem" tabindex="-1" onClick="addFunds('50.00');">$ 50.00</a></li>
                    <li role="amount"><a role="menuitem" tabindex="-1" onClick="addFunds('100.00');">$ 100.00</a></li>
                    <li role="amount"><a role="menuitem" tabindex="-1" onClick="addFunds('200.00');">$ 200.00</a></li>
                    <li role="amount"><a role="menuitem" tabindex="-1" onClick="addFunds('500.00');">$ 500.00</a></li>
                    <li role="amount"><a role="menuitem" tabindex="-1" onClick="addFunds('1000.00');">$ 1000.00</a></li>
                  </ul>
                </div>
            </div>
        </td>
    </tr>


    <tr>
        <td class="col-xs-3">
            <h5><strong><span class="required">* </span>API Key:</strong></h5>
            <span class="help"><i class="fa fa-info-circle"></i>&nbsp;Get your API key from <a href="http://smsbump.com">http://smsbump.com</a>.</span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <div class="form-group" id="api_input" >
                    <input type="text" id="APIKey" class="form-control" name="SMSBump[APIKey]" value="<?php if(isset($data['SMSBump']['APIKey'])) { echo $data['SMSBump']['APIKey']; } else { echo ""; }?>" />
                    <input type="hidden" id="APISecret" class="form-control" name="SMSBump[APISecret]" value="<?php if(isset($data['SMSBump']['APISecret'])) { echo $data['SMSBump']['APISecret']; } else { echo ""; }?>" />
                </div>
            </div>
        </td>
    </tr>
   
</table>

<script>

$.ajax({

  url: 'https://api.smsbump.com/balance/<?php echo $data['SMSBump']['APIKey'] ?>.json',
  type: "GET",
  async: true,
  success: function(result) {  
    if (result.data){
        $('.fa-spinner').css('display','none');
        var balance = parseFloat(result.data.balance).toFixed(2);
        $('#balance').html(balance + ' <span style="text-transform:uppercase;">' + result.data.currency + '</span>');
        
        if (balance > 0) {
            $(function() {
                var $typeSelector = $('#module_status');
                var $toggleArea = $('#status');
                 if ($typeSelector.val() === 'yes') {
                        $toggleArea.removeClass('label label-danger')
                        $toggleArea.css('display','inline-block');
                        $toggleArea.addClass('label label-success') ;
                        $toggleArea.html('Enabled');
                    }
                    else {
                        $toggleArea.removeClass('label label-success');
                        $toggleArea.css('display','inline-block');
                        $toggleArea.addClass('label label-danger'); 
                        $toggleArea.html('Disabled');
                    }
                $typeSelector.change(function(){
                    if ($typeSelector.val() === 'yes') {
                        $toggleArea.removeClass('label label-danger')
                        $toggleArea.css('display','inline-block');
                        $toggleArea.addClass('label label-success') ;
                        $toggleArea.html('Enabled');
                    }
                    else {
                        $toggleArea.removeClass('label label-success')
                        $toggleArea.css('display','inline-block')
                        $toggleArea.addClass('label label-danger')  
                        $toggleArea.html('Disabled');
                    }
                }); 
            });
        } else if (balance <= 0) {

            $('#status').removeClass('label label-success');
            $('#status').removeClass('label label-danger');
            $('#status').css('display','inline')
            $('#status').addClass('label label-warning') 
            $('#status').html('Low credit'); 

        } 
    } else if (result.status == "error"){
            $('.fa-spinner').css('display','none');
            error = result.message;
            $('#status').removeClass('label label-success');
             $('#status').addClass('label label-danger');

            if(error.indexOf('Missing API') > -1) {
                $('#status').html("Missing API key"); 
            } else if(error.indexOf('This app is stopped') > -1) {
                $('#status').html("Incorrect API key"); 
            } else {
            $('#status').html("Disabled"); 
            }
    
        }
    }
});

</script>

