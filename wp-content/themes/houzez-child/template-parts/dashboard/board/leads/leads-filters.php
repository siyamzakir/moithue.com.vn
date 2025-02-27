<?php
/**
 * Leads filtering form template
 */
global $dashboard_crm, $hpage, $name, $phone, $start_date, $end_date, $referrer, $reset_url;

?> 

<!-- Leads Filter Modal -->
<div class="modal fade" id="leadsFilterModal" tabindex="-1" role="dialog" aria-labelledby="leadsFilterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leadsFilterModalLabel">
                    <i class="houzez-icon icon-filter mr-1"></i> <?php esc_html_e('Filter Leads', 'houzez'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="leads-filter-form" method="GET">
                    <!-- Keep existing parameters -->
                    <input type="hidden" name="hpage" value="<?php echo esc_attr($hpage); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Name', 'houzez'); ?></label>
                                <input type="text" class="form-control" name="lead_name" 
                                    value="<?php echo esc_attr($name); ?>" 
                                    placeholder="<?php esc_attr_e('Search by name...', 'houzez'); ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Phone', 'houzez'); ?></label>
                                <input type="text" class="form-control" name="phone" 
                                    value="<?php echo esc_attr($phone); ?>"
                                    placeholder="<?php esc_attr_e('Search by phone...', 'houzez'); ?>"
                                />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Date ~ From: - To:', 'houzez'); ?></label>
                                <div class="input-group">
                                    <input type="date" class="form-control date" 
                                        id="start_date" 
                                        name="start_date" 
                                        value="<?= $start_date ?? ''; ?>"
                                        placeholder="00-00-00"
                                    />
                                    <input type="date" class="form-control date" 
                                        id="end_date" 
                                        name="end_date" 
                                        value="<?= $end_date ?? ''; ?>"
                                        placeholder="YYYY-MM-DD"
                                    />
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-light clear-date border" title="Clear Date" clear-date-id=".date">
                                            <i class="fas fa-times text-danger"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Referrer', 'houzez'); ?></label>
                                <?php
                                    $sources = hcrm_get_option('source', 'hcrm_lead_settings', 'Website, Newspaper, Friend, Google, Facebook');
                                    echo Section::referrerSelector($referrer, '', 'name="referrer"', 'Select A Referrer', '<option value="">Not Selected</option', $sources)
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer flex-wrap">
                        <button type="submit" class="btn btn-primary mb-2 mb-sm-0" id="apply_filters" name="apply_filters" value="true">
                            <i class="houzez-icon icon-search mr-1"></i> <?php esc_html_e('Apply Filters', 'houzez'); ?>
                        </button>
                        <a href="<?= $reset_url; ?>" class="btn btn-success mb-2 mb-sm-0">
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
    
    #leadsFilterModal .modal-content {
        border-radius: 4px;
    }

    #leadsFilterModal .modal-body {
        padding: 20px;
    }

    #leadsFilterModal .form-group {
        margin-bottom: 15px;
    }

    #leadsFilterModal .selectpicker {
        width: 100%;
    }

    #leadsFilterModal .modal-footer {
        border-top: 1px solid #dee2e6;
        padding: 15px;
        gap: 10px;
    }

    /* Responsive styles */
    @media (max-width: 767px) {
        #leadsFilterModal .modal-footer {
            justify-content: center;
        }
        
        #leadsFilterModal .btn {
            width: 100%;
        }
        
        #leadsFilterModal .col-md-6 {
            margin-bottom: 15px;
        }
    }
</style>

<script>
    (function($){
        $(document).ready(function(){
            // Initialize Bootstrap Select
            $('.selectpicker').selectpicker();
            
            // Initialize Datepicker if needed
            $('input[name="date"]').on('change', function() {
                $(this).attr('data-date', $(this).val());
            });

            $('[clear-date-id]').on('click', function(){
                var clearDateId = $(this).attr('clear-date-id');
                $(clearDateId).val('');
            });
        });
    })(jQuery);
</script>