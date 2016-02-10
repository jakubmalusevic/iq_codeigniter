<form method="post" action="<?=base_url()?>share/listing/update/<?=$item->id?>" class="modal-wrapper column-4" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?>">
    <div class="modal-header">
        <?=$this->lang->line("edit_share")?>
    </div>    
    <div class="modal-content">
        <div class="inline-form-row">
            <div class="column-6">
                <label for="name"><?=$this->lang->line("name")?></label>
            </div>
            <div class="column-6">
                <input type="text" id="name" name="name" value="<?=$item->name?>" class="full-width" required-field="true" validation="[not-empty]" />
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="inline-form-row">
            <div class="column-6">
                <label for="buy_price"><?=$this->lang->line("buy_price")?></label>
            </div>
            <div class="column-6">
                <input type="text" id="buy_price" name="buy_price" value="<?=$item->buy_price?>" class="full-width" required-field="true" validation="[not-empty]" />
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="inline-form-row">
            <div class="column-6">
                <label for="sell_price"><?=$this->lang->line("sell_price")?></label>
            </div>
            <div class="column-6">
                <input type="text" id="sell_price" name="sell_price" value="<?=$item->sell_price?>" class="full-width" required-field="true" validation="[not-empty]" />
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="inline-form-row">
            <div class="column-6">
                <label for="quantity"><?=$this->lang->line("quantity")?></label>
            </div>
            <div class="column-6">
                <input type="text" id="quantity" name="quantity" value="<?=$item->quantity?>" class="full-width" required-field="true" validation="[not-empty]" />
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="inline-form-row">
            <div class="column-6">
                <label for="commission"><?=$this->lang->line("commission")?></label>
            </div>
            <div class="column-6">
                <textarea id="commission" name="commission" class="full-width"><?=$item->commission?></textarea>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="form-error-handler" error-handler="true"></div>
    </div>    
    <div class="modal-footer">
        <input type="submit" value="<?=$this->lang->line("update")?>" class="button medium-button primary-button" />
        <a href="#" class="button medium-button secondary-button close-modal-window">
            <?=$this->lang->line("close")?>
        </a>        
    </div>
</form>