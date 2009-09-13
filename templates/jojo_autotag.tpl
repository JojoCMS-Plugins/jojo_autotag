<br/>
<p>Enter a tag term to search for all relevant pages that mention it and add it as a tag for each result</p>
<form name="search1" action="" method="post">
    <input name="q" value="{$keywords}" size="35" style="vertical-align:middle" />
    <select name="type" style="font-size: 90%">
        <option value="" {if $searchtype == ''}selected="selected"{/if}>any</option>
        <option value="all" {if $searchtype == 'all'}selected="selected"{/if}>all</option>
        <option value="phrase" {if $searchtype == 'phrase'}selected="selected"{/if}>exact</option>
    </select>
{if _MULTILANGUAGE && (count($languages)>1)}
    <select name="l" style="font-size: 90%">
        <option value=""{if $language == ''} selected="selected"{/if}>All Languages</option>
{foreach from=$languages key=code item=name}
        <option value="{$code}"{if $language == $code} selected="selected"{/if}>{$name|escape:"html":$charset}</option>
{/foreach}
    </select>
{/if}
    <input class="button" type="submit" value="TagSearch" />
<br/>
<br/>
    <p>update with this Tag: <input name="term" value="{$keywords}" size="15" style="vertical-align:middle" />
    <input class="button" type="submit" id="update" value="Update Tags" /></p>

{if $results}

    {if $OPTIONS.search_relevance =='yes'}<div class="search-relevance">Relevance</div>{/if}
   <p>{$numresults} Page results for <b>{$keywords}</b>.</p>
    <p class="links" id="search-filter">Filter results by: <a href="#" onclick="filterresults();return false;" id="filter-search-none" class="current-filter">None</a> 
{foreach from=$resulttypes item=cat}
   | <a href="#" onclick="filterresults('search-cat-{$cat|strtolower|replace:' ':'-'}');return false;"  id="filter-search-cat-{$cat|strtolower|replace:' ':'-'}">{$cat}</a>&nbsp;
{/foreach}
    </p>
     <p class="links">Filter results by: <a href="#" onclick="$('.addtag').show();$('.deletetag').hide();return false;">To add</a> | <a href="#" onclick="$('.addtag').hide();$('.deletetag').show();return false;">To delete</a> | <a href="#" onclick="$('.addtag').show();$('.deletetag').show();return false;">None</a></p>
  
{foreach from=$results item=result}
 {foreach from=$result.tags item=tag}{if $tag.cleanword==$keywords}{assign var=tagged value=true}{/if}{/foreach}
 <div class="search-result search-cat-{$result.type|strtolower|replace:' ':'-'} {if $tagged}deletetag{else}addtag{/if}" style="clear:both;">
    {if $OPTIONS.search_relevance =='yes'}<div class="search-relevance-display" style="width:{$result.displayrelevance|string_format:"%d"}px;" title="Search relevance: {$result.relevance|string_format:"%.1f"}"></div>{/if}
    <h3><a href="{$result.url}" title="{$result.title}">{$result.title}</a></h3>
    {if $result.image && $OPTIONS.search_images =='yes'}<a href="{$result.url}" title="{$result.title|escape:"html":$charset}" rel="nofollow"><img src="images/{if $OPTIONS.search_image_format}{$OPTIONS.search_image_format}{else}v6000{/if}/{$result.image}" class="float-right" alt="{$result.title}" /></a>{/if}
    <p>{$result.body}<p>
    <p class="links">Tagged with: 
    {assign var=tagged value=false}
    {foreach from=$result.tags item=tag}<a href="{if _MULTILANGUAGE}{$pg_language}/{/if}tags/{$tag.url}/">{if $tag.cleanword==$keywords}{assign var=tagged value=true}<b>{$tag.cleanword}</b>{else}{$tag.cleanword}{/if}</a> | {/foreach}
    </p>
    <p><input type="radio" name="updates[{$result.id},{$result.plugin}]" value="add" /> {if $tagged==false}<span style ="color:#E09232">Add</span>{else}Add{/if} &nbsp;<input type="radio" name="updates[{$result.id},{$result.plugin}]" value="delete" /> {if $tagged!=false}<span style ="color:#E09232">Delete</span>{else}Delete{/if}</p>
    <p class="links">{$result.type}: <a href="{$result.absoluteurl}" title="{$result.title}" class="links">&gt; {$result.displayurl}</a></p>
  </div>
{/foreach}
    <br />
    <input class="button" type="submit" id="update" value="Update Tags" />
</form>

<script type="text/javascript">{literal}
/*<![CDATA[*/
function filterresults(cat) {
    if (cat) {
        $('#search-filter a').removeClass('current-filter');
        var filter = '#filter-'+cat;
        $(filter).addClass('current-filter');
        $(".search-result").hide();
        $("."+cat).show();
    } else {
       $(".search-result").show();
        $('#search-filter a').removeClass('current-filter');
        $('#filter-search-none').addClass('current-filter');
    }
}
/*]]>*/{/literal}
</script>

{elseif $keywords}
    <p>There were no page results to tag for <b>{$keywords}</b>.</p>
{/if}
