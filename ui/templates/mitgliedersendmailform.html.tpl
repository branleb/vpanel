{include file="header.html.tpl" ansicht="Mail verschicken"}
<p class="pagetitle">Mail verschicken</p>
<form action="{"mitglieder_sendmail.preview"|___}" method="post">
 <fieldset>
  <table>
  <tr>
   <th>{"Filter:"|__}</th>
   <td>
    <select name="filterid"><option value="">{"(kein Filter)"|__}</option>{foreach from=$filters item=filter}<option value="{$filter.filterid|escape:html}" {if $smarty.request.filterid == $filter.filterid}selected="selected"{/if}>{$filter.label|escape:html}</option>{/foreach}</select>
   </td>
  </tr>
  <tr>
   <th>{"Mailvorlage:"|__}</th>
   <td>
    <select name="mailtemplateid">{foreach from=$mailtemplates item=mailtemplate}<option value="{$mailtemplate.templateid|escape:html}" {if $smarty.request.mailtemplateid == $mailtemplate.templateid}selected="selected"{/if}>{$mailtemplate.label|escape:html}</option>{/foreach}</select>
   </td>
  </tr>
  <tr>
   <th colspan="2"><input class="submit" type="submit" name="save" value="{"Weiter"|__}" /></th>
  </tr>
  </table>
 </fieldset>
</form>
{include file="footer.html.tpl"}