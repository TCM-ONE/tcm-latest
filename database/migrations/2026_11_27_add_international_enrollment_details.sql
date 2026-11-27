ALTER TABLE payment_submissions
    ADD COLUMN IF NOT EXISTS international_details JSON DEFAULT NULL;
