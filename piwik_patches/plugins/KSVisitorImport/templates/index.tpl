{assign var=showSitesSelection value=false}
{assign var=showPeriodSelection value=false}
{include file="CoreAdminHome/templates/header.tpl"}

<h2>{'KSVisitorImport_KSVisitorImport'|translate}</h2>
<p>{'KSVisitorImport_PluginDescription'|translate}</p>
{if $message neq ""}
	<div class="ui-widget">
		<div class="ui-inline-help ui-state-highlight ui-corner-all">
			<span class="ui-icon ui-icon-info" style="float:left;margin-right:.3em;"></span>
			{$message|translate}
		</div>
	</div>
{/if}
{if $preview != NULL}
    <p>{'KSVisitorImport_Preview_Information'|translate}</p>
	<table class="preview">
	<tr>
	{foreach from=$preview[0] key=key item=value}
		{if $key != 'fullString' && $key != 'fullDateTime'}
			<th>{$key}</th>
		{/if}
	{/foreach}
	</tr>


	{foreach from=$preview item=record}
		<tr>
		{foreach from=$record item=value key=key}
			{if $key != 'fullString' && $key != 'fullDateTime'}
				<td>{$value}</td>
			{/if}
		{/foreach}
		</tr>
	{/foreach}
	</table>
{/if}

<form method="POST" action="{url module=KSVisitorImport action=generate}">
	<table class="adminTable">
		<tr>
		    <td><label for="idSite">{'General_ChooseWebsite'|translate}</label></td>
		    <td>{html_options name=idSite options=$sitesList selected=$seed.idsite}</td>
		</tr>
		<tr>
		    <td><label for="path">{'KSVisitorImport_path'|translate}</label></td>
		    <td><input type="text" value="{$seed.path}" name="path" id="path" /></td>
		</tr>
		<tr>
		    <td><label for="logfiletype">{'KSVisitorImport_logfiletype'|translate}</label></td>
		    <td>{html_options name=logfiletype options=$logfilesList selected=$seed.logfiletype}</td>
		</tr>
		<tr>
		    <td><label for="keepLogs">{'KSVisitorImport_keepLogs'|translate}</label></td>
		    <td><input type="checkbox" value="1" name="keepLogs" id="keepLogs" {$seed.keepLogsChecked} /></td>
		</tr>
		<tr>
		    <td><label for="debug">{'KSVisitorImport_debug'|translate}</label></td>
		    <td><input type="checkbox" value="1" name="debug" id="debug" {$seed.debugChecked} /></td>
		</tr>
		<tr>
		    <td><label for="choice">{'KSVisitorImport_AreYouSure'|translate}</label></td>
		    <td>
				<input type="checkbox" name="choice" id="choice" value="yes" /> <label for="choice">{'KSVisitorImport_ChoiceYes'|translate}</label><br />
				<p>{'KSVisitorImport_Warning'|translate}<br />
				   {'KSVisitorImport_NotReversible'|translate:'<b>':'</b>'}</p>
			</td>
		</tr>
	</table>
	<input type="submit" value="{'KSVisitorImport_Preview'|translate}" name="preview" class="submit" />
	<input type="submit" value="{'KSVisitorImport_StartImport'|translate}" name="import" class="submit" />
	<input type="hidden" value="{$token_auth}" name="token_auth" />
	<input type="hidden" value="{$nonce}" name="form_nonce" />
</form>
<table class="adminTable">
	<tr>
	    <td><label>{'KSVisitorImport_basedon'|translate}</label></td>
	    <td>
			<ul>
				<li><a href="http://forge.typo3.org/issues/11791">http://forge.typo3.org/issues/11791</a></li>
				<li><a href="http://jaymz.eu/2010/02/importing-existing-visitor-stats-from-google-analytics-to-piwik/">http://jaymz.eu/2010/02/importing-existing-visitor-stats-from-google-analytics-to-piwik/</a></li>
				<li><a href="http://www.cabag.ch/typo3-extensions/typo3-schnittstellen/piwik-apache-import.html">http://www.cabag.ch/typo3-extensions/typo3-schnittstellen/piwik-apache-import.html</a></li>
			</ul>
		</td>
	</tr>
</table>
{include file="CoreAdminHome/templates/footer.tpl"}
