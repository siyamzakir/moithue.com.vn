<?php
/**
 * Deals filtering form template
 */

global $dashboard_crm, $has_permission, $user_id, $hpage, $deal_group, $deals, $submit_filters, $active_deals, $won_deals, $lost_deals, $property_id, $lead_id, $agent_id, $deal_title, $next_action, $due_date, $lead_email, $lead_mobile, $tabs, $status;
?>

<!-- Deals Filter Modal -->
<div class="modal fade" id="dealsFilterModal" tabindex="-1" role="dialog" aria-labelledby="dealsFilterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dealsFilterModalLabel"><?php esc_html_e('Filter Deals', 'houzez'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="deals-filter-form" method="GET">
                    <!-- Keep existing parameters -->
                    <input type="hidden" name="hpage" value="<?php echo esc_attr($hpage); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Deal Title', 'houzez'); ?></label>
                                <input type="text" class="form-control" name="deal_title" 
                                    value="<?php echo esc_attr($deal_title ?? ''); ?>" 
                                    placeholder="<?php esc_attr_e('Search by deal name...', 'houzez'); ?>">
                            </div>
                        </div>

                        <!-- Filter By Property -->
                        <div class="col-md-6">
                            <?= Section::postSelectBox('property', 'property_id', 'Property', 'Select A Property', [], [$property_id], 'true', $user_id, "<option value=''>No Property</option>"); ?>
                        </div>

                        <!-- Filter By Lead/User -->
                        <?php if ($has_permission) : ?>
                            <div class="col-md-6">
                                <?= Section::leadSelectBox('lead_id', 'Contact Name', 'Select Contact', [], [$lead_id], 'true', $user_id, "<option value=''>No Contact</option>"); ?>
                            </div>

                            <!-- Filter By Agent -->
                            <div class="col-md-6">
                                <?= Section::userSelectBox('agent_id', 'Agent', 'Select Agent', [], [$agent_id], 'true', "<option value=''>No Agent</option>"); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Filter deals by next action -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Next Action', 'houzez'); ?></label>
                                <?= Section::nextActionSelector($next_action, '', 'name="next_action"', 'Select a Action', "<option value=''>No Action</option>"); ?>
                            </div>
                        </div>

                        <!-- Filter deals by next action due date -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Action Due Date', 'houzez'); ?></label>
                                <div class="input-group">
                                    <input type="date" class="form-control" 
                                        id="due_date" 
                                        name="due_date" 
                                        value="<?= $due_date ?? ''; ?>"
                                        placeholder="YYYY-MM-DD"
                                    />
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-light clear-date border" title="Clear Date" clear-date-id="#due_date">
                                            <i class="fas fa-times text-danger"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter deals by phone number -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Phone Number', 'houzez'); ?></label>
                                <input type="text" class="form-control" name="lead_mobile" 
                                    value="<?= $lead_mobile ?? ''; ?>"
                                    placeholder="Search by phone..."
                                />
                            </div>
                        </div>
                        
                        <!-- Filter deals by phone number -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Email', 'houzez'); ?></label>
                                <input type="text" class="form-control" name="lead_email" 
                                    value="<?= $lead_email ?? ''; ?>"
                                    placeholder="Search by email..."
                                />
                            </div>
                        </div>

                        <!-- Filtered By Deal Status -->
                        <div class="col-md-6">
                            <label><?php esc_html_e('Deal Status', 'houzez'); ?></label>
                            <?= Section::dealStatusSelector($status, 'status', 'name="status"', 'Select a Deal Status', "<option value=''>No Deal Status</option>"); ?>
                        </div>

                        <!-- 
                            - Filter deals by their status group (active/won/lost)
                            - The selected group determines which deals are displayed in the table
                        -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Group By Status', 'houzez'); ?></label>
                                <select class="selectpicker form-control bs-select-hidden" 
                                    title="<?php esc_html_e('Select Group By Status', 'houzez'); ?>"
                                    name="tab"    
                                >
                                    <?php foreach ($tabs as $tab) : ?>
                                        <option value="<?php echo esc_attr($tab); ?>"
                                            <?php echo $deal_group == $tab ? 'selected' : ''; ?>
                                        ><?php echo ucfirst(esc_html($tab)); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-wrap">
                        <button type="submit" class="btn btn-primary mb-2 mb-sm-0">
                            <i class="houzez-icon icon-search mr-1"></i> <?php esc_html_e('Apply Filters', 'houzez'); ?>
                        </button>
                        <a href="<?php echo esc_url(add_query_arg(['hpage' => $hpage, 'tab' => $deal_group], $dashboard_crm)); ?>" class="btn btn-success mb-2 mb-sm-0">
                            <i class="houzez-icon icon-reload mr-1"></i> <?php esc_html_e('Reset Filters', 'houzez'); ?>
                        </a>
                        <button type="button" class="btn btn-secondary mb-2 mb-sm-0" data-dismiss="modal">
                            <?php esc_html_e('Close', 'houzez'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .filter-button-wrap {
        margin: 20px 0;
    }
    
    #dealsFilterModal .modal-content {
        border-radius: 4px;
    }

    #dealsFilterModal .modal-body {
        padding: 20px;
    }

    #dealsFilterModal .form-group {
        margin-bottom: 15px;
    }

    #dealsFilterModal .selectpicker {
        width: 100%;
    }

    #dealsFilterModal .modal-footer {
        border-top: 1px solid #dee2e6;
        padding: 15px;
        gap: 10px;
    }

    /* Responsive styles */
    @media (max-width: 767px) {
        #dealsFilterModal .modal-footer {
            justify-content: center;
        }
        
        #dealsFilterModal .btn {
            width: 100%;
        }
        
        #dealsFilterModal .col-md-6 {
            margin-bottom: 15px;
        }
    }
</style> 

<script>
    (function($){
        $(document).ready(function(){
            $('[clear-date-id]').on('click', function(){
                var clearDateId = $(this).attr('clear-date-id');
                $(clearDateId).val('');
            });
        });
    })(jQuery);
</script>