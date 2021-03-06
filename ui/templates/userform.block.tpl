<form action="{if isset($user)}{"users_details"|___:$user.userid}{else}{"users_create"|___}{/if}" method="post" class="form-horizontal">
 <fieldset>
<div class="control-group">
    <label class="control-label" for="username">{"Username:"|__}</label>
    <div class="controls">
        <input class="input" type="text" name="username" value="{if isset($user)}{$user.username|escape:html}{/if}" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="password">{"Passwort:"|__}</label>
    <div class="controls">
        <input class="password" type="password" name="password" id="password"/><span class="help-inline" style="opacity:0;">{"Bitte ein Passwort angeben."|__}</span>
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="apikey">{"APIKey:"|__}</label>
    <div class="controls">
        <label class="radio inline"><input type="radio" name="apikey" value="generate" /> {"Generieren"|__}</label>
        <label class="radio inline"><input type="radio" name="apikey" value="remove" /> {"Entfernen"|__}</label>
        <p class="help-block">{$user.apikey}</p>
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="aktiv">{"Aktiv:"|__}</label>
    <div class="controls">
        <input type="checkbox" name="aktiv" {if !isset($user) || $user.aktiv}checked="checked"{/if} />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="defaultgliederungid">{"Standard-Gliederung:"|__}</label>
    <div class="controls">
        {if isset($user)}
            {include file=gliederungdropdown.block.tpl fieldname=defaultgliederungid defaulttext="(Keine)" selectedgliederungid=$user.defaultgliederungid}
        {else}
            {include file=gliederungdropdown.block.tpl fieldname=defaultgliederungid defaulttext="(Keine)"}
        {/if}
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="defaultdokumentkategorieid">{"Standard-Dokumentenkategorie:"|__}</label>
    <div class="controls">
        {if isset($user)}
            {include file=dokumentkategoriedropdown.block.tpl fieldname=defaultdokumentkategorieid defaulttext="(Übersicht)" selecteddokumentkategorieid=$user.defaultdokumentkategorieid}
        {else}
            {include file=dokumentkategoriedropdown.block.tpl fieldname=defaultdokumentkategorieid defaulttext="(Übersicht)"}
        {/if}
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="defaultdokumentstatusid">{"Standard-Dokumentenstatus:"|__}</label>
    <div class="controls">
        {if isset($user)}
            {include file=dokumentstatusdropdown.block.tpl fieldname=defaultdokumentstatusid defaulttext="(Übersicht)" selecteddokumentstatusid=$user.defaultdokumentstatusid}
        {else}
            {include file=dokumentstatusdropdown.block.tpl fieldname=defaultdokumentstatusid defaulttext="(Übersicht)"}
        {/if}
    </div>
</div>
<div class="form-actions">
    <button class="btn btn-primary submit" type="submit" name="save" value="1">{"Speichern"|__}</button>
    <button class="btn">{"Abbrechen"|__}</button>
</div>
</fieldset>
</form>
