-- Database that need to be transform
-- This database is contain all the newest data
-- But might have different schema
-- 'packaging' is database that has newest schema but doesn't contain any newest data
-- Only template data
use gema_tmp;

start transaction;

-- Diable FK while update
-- SET FOREIGN_KEY_CHECKS=0;

-- ===== Renew category based on group_by =====
-- Delete all group_by that want to re new
-- delete from category where group_by IN (
--     'permission', 'DELIMITER'
-- );

-- Re insert all group_by
-- insert into category (
--     select * from packaging.category where group_by IN (
--         'permission', 'DELIMITER'
--     )
-- );

-- Enable FK when done
-- SET FOREIGN_KEY_CHECKS=1;

-- ===== update View stock flow ======
-- update delivery_order_detail WHERE storage_id == 1 (is_storage == 1) TO storage_id == 3 (PUSAT A-1) (is_storage == 0)
update delivery_order_detail 
    set storage_id = 3 
    where delivery_order_id 
    in ( select v.id from `view_stock_flow` v join `storage` s on s.id = v.storage_id where is_storage = 1 and category = 'delivery-order' );
commit;
