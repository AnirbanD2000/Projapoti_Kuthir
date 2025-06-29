-- Update existing biodata_images table to add approval_status column
USE wedding_website;

-- Add approval_status column if it doesn't exist
ALTER TABLE biodata_images 
ADD COLUMN IF NOT EXISTS approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending';

-- Update existing records to be approved (so they show up on the main page)
UPDATE biodata_images SET approval_status = 'approved' WHERE approval_status IS NULL OR approval_status = 'pending';

-- Add index for better performance
ALTER TABLE biodata_images ADD INDEX idx_approval_status (approval_status); 