{if isset($input.display_image)}
    {$input.display_image}
{/if}
<input type="file" name="{$input.name}" id="{$input.id}" {if isset($input.value)}value="{$input.value}"{/if} {if isset($input.size)}size="{$input.size}"{/if} {if isset($input.required) && $input.required}required="required"{/if} />

{if isset($input.selected_categories)}
    {foreach $input.tree.categories as $category}
        <input type="checkbox" name="{$input.name}[]" id="{$input.id}_{$category.id}" value="{$category.id}" {if in_array($category.id, $input.selected_categories)}checked="checked"{/if} />
        <label for="{$input.id}_{$category.id}">{$category.name}</label>
    {/foreach}
{/if}