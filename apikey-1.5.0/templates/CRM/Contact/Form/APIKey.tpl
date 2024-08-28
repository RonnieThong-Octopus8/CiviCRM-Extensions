{* this template is used to add/edit the API Key for a contact *}

<div class="form-item">
  <fieldset><legend>{ts}API Key{/ts}</legend>
    <div class="crm-block crm-form-block crm-cividesk-api-form-block">
      <table class="form-layout-compressed">
        <tr style="display:none;" class="crm-apikey-form-block">
          <td class="label"></td>
          <td><div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div></td>
        </tr>
        <tr style="display:none;" class="crm-apikey-form-block">
          <td class="label"></td>
          <td></td>
        </tr>
        <tr class="crm-apikey-form-block">
          <td class="label">{$form.api_key.label}</td>
          <td>
            {$form.api_key.html}&nbsp;{crmButton style="display:inline-block;vertical-align:middle;float:none!important;" href="javascript:void(0);" id="api_key_generate" class="generate-apikey" title="Generate API Key" icon="fa-key"}{ts}Generate{/ts}{/crmButton}
          </td>  
        </tr>
        <tr class="crm-apikey-form-block">
          <td class="label"></td>
          <td>
            <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
          </td>
        </tr>
      </table>
    </div>
  </fieldset>
</div>
                                            
<style type="text/css">
{literal}
#crm-container .crm-error {
    padding: 0;
}
{/literal}
</style>
                 

{literal}
<script type="text/javascript">
cj(function(){
  cj('#api_key_generate').on('click', function() {
    cj('#api_key').val(randomString(24));
    return true;
  });
});

function randomString(length, charset) {
  var text = "";
  charset = charset || "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  for( var i=0; i < length; i++ )
    text += charset.charAt(Math.floor(Math.random() * charset.length));
  return text;
}
</script>
{/literal}
                                    