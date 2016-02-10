<section class="content">
    <div class="content-inner">
        <div class="tabs-wrapper">
            <ul class="tabs-list" id="tabs-list">
                <li class="active">
                    <a href="#">
                        <?=$this->lang->line("share")?>
                        <?php
                        $_section_description=$this->language->getSectionDescription("share","listing");
                        if ($_section_description!="") {
                        ?>
                        <i class="typcn typcn-info-large" tooltip-text="<?=$_section_description?>"></i>
                        <?php
                        }
                        ?>
                    </a>
                </li>
            </ul>
            <script>
            var _tabs=new Tabs("#tabs-list").bindEvents();
            </script>
            <div class="clearfix"></div>
        </div>
        <div class="content-header">            
            <a href="<?=base_url()?>share/listing/create" class="button big-button primary-button modal-window">
                <i class="typcn typcn-plus"></i>                
                <?=$this->lang->line("create_share")?>
            </a>                                
        </div>    
        <div class="content-body">
  
            <div class="content-action">
                    <div class="content-action-inner">
                    <div class="content-action-header xs-static-hide"></div>
                    <div class="content-action-subheader">
                    </div>
                    <table class="work-table" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>                                
                                <th sort-column="share_records.name" <?=@$_GET['sort-column']==""?"sort-direction=\"asc\"":""?>><?=$this->lang->line("share_name")?></th>
                                <th sort-column="share_records.buy_price"><?=$this->lang->line("buy_price")?></th>
                                <th sort-column="share_records.sell_price" class="s-static-hide"><?=$this->lang->line("sell_price")?></th>
                                <th sort-column="share_records.quantity"><?=$this->lang->line("quantity")?></th>
                                <th sort-column="share_records.commission" class="xs-static-hide"><?=$this->lang->line("commission")?></th>
                                <th class="xs-static-hide">P/L</th>
                                <th class="xs-static-hide"><?=$this->lang->line("state")?></th>
                                <th></th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for($i=0;$i<count($items);$i++) {
                            ?>
                            <tr>
                                <td class="align-center"><?=$items[$i]->name?></td>
                                <td class="align-center"><?=$items[$i]->buy_price?></td>
                                <td class="align-center"><?=$items[$i]->sell_price?></td>
                                <td class="align-center"><?=$items[$i]->quantity?></a></td>
                                <td class="align-center"><?=$items[$i]->commission?></td>
                                <td class="align-center"><?php echo(($items[$i]->buy_price - $items[$i]->sell_price) * $items[$i]->quantity);?></td>
                                <td class="s-static-hide align-center">
                                    <?php                                    
                                    if ($items[$i]->state==1) {
                                    ?>
                                    <a href="<?=base_url()?>share/listing/changestate/<?=$items[$i]->id?>?state=0" class="table-action-button green-icon" tooltip-text="<?=$this->lang->line("disable_module")?>" ><i class="typcn typcn-tick"></i></a>
                                    <?php
                                    } else {
                                    ?>
                                    <a href="<?=base_url()?>share/listing/changestate/<?=$items[$i]->id?>?state=1" class="table-action-button red-icon" tooltip-text="<?=$this->lang->line("enable_module")?>" ><i class="typcn typcn-delete"></i></a>
                                    <?php
                                    }
                                    ?>                                    
                                </td>                            
                                <td class="align-center">                                    
                                    <a href="<?=base_url()?>share/listing/update/<?=$items[$i]->id?>" class="table-action-button modal-window" tooltip-text="<?=$this->lang->line("edit_client")?>" ><i class="typcn typcn-pencil"></i></a>                                    
                                    <a href="<?=base_url()?>share/listing/delete/<?=$items[$i]->id?>" class="table-action-button popup-action" popup-type="confirmation" popup-message="<?=$this->lang->line("you_really_want_to_delete_client")?>" popup-buttons="confirm:<?=$this->lang->line("yes")?>,close:<?=$this->lang->line("cancel")?>" tooltip-text="<?=$this->lang->line("delete_client")?>"><i class="typcn typcn-trash"></i></a>                                                                    
                                </td>                                
                            </tr>
                            <?php
                            }
                            if (count($items)==0) {
                            ?>
                            <tr>
                                <td class="no-records-found-row" colspan="5"><?=$this->lang->line("no_records_found")?></td>
                            </tr>
                            <?php
                            }
                            ?>                            
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>    
            <div class="content-footer">
                <?php
                $this->pagination->drawPagination();
                ?>
            </div>                        
        </div>        
    </div>
</section>