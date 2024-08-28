{* this template is used to display the 'API Key' tab for a contact *}

<div class="form-item">
  <fieldset><legend>{ts}API Keys{/ts}</legend>
    <div class="crm-block crm-form-block crm-cividesk-apikey-form-block">
      {if $isAdmin || $canView}
        {if $apiKey}
          <div class="label" style="float:left">{ts}Your API key is{/ts}:&nbsp;</div>
          <div style="float:left"><strong>{$apiKey}</strong></div><br />
          <br />
        {/if}
        {if $canEdit}
        <div class="action-link">
          {crmButton p="$addEditApiKeyPath" q="$addEditApiKeyQuery" class="edit-apikey" title="$addEditApiButtonString" icon="$addEditApiButtonIcon"}{$addEditApiButtonString}{/crmButton}
        </div>
        {/if}
      {else}
        <div>{ts}You are not authorized to display this API Key.{/ts}</div>
      {/if}
    </div>
    <div class="crm-block crm-form-block crm-cividesk-sitekey-form-block">
      {if $isAdmin || $canViewSiteKey}
        {if $siteKey}
          <div class="label" style="float:left">{ts}The Site Key is{/ts}:&nbsp;</div>
          <div style="float:left"><strong>{$siteKey}</strong></div><br />
          <br />
        {/if}
      {else}
        <div>{ts}You are not authorized to display the Site Key.{/ts}</div>
      {/if}
    </div>
  </fieldset>
</div>

