-- Allow a TCM One user to receive partner access without replacing their existing role or password.
ALTER TABLE partner_memberships
    ADD COLUMN member_role ENUM('partner_owner','partner_staff') NOT NULL DEFAULT 'partner_staff' AFTER user_id;

UPDATE partner_memberships pm
JOIN users u ON u.id = pm.user_id
SET pm.member_role = CASE WHEN u.role = 'partner_owner' THEN 'partner_owner' ELSE 'partner_staff' END;
