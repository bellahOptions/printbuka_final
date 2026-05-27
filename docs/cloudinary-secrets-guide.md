# Cloudinary Integration – Setup Guide

## Environment Variables

Add these to your `.env` file:

```ini
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
CLOUDINARY_SECURE=true
CLOUDINARY_DEFAULT_FOLDER=printbuka
```

## How it works

1. **Upload flow**: When an image file is uploaded via any controller or Livewire component, it is:
   - Stored locally on the `public` disk as before (fallback)
   - Uploaded to Cloudinary under the `printbuka/` folder
   - The database stores the **Cloudinary public_id** in the `path` field (e.g. `printbuka/job-assets/images/file.jpg`)

2. **Display flow**: When any view calls `MediaUrl::resolve($path)`:
   - If the path is a Cloudinary resource, it returns the full Cloudinary CDN URL
   - If Cloudinary is not configured, it falls back to local storage URL

3. **Local fallback**: Local storage still works as a backup. If Cloudinary is down or not configured, the system uses local `storage/app/public/` paths.

## Images affected

All of the following now upload to Cloudinary:
- Product featured images & gallery images
- Blog featured images & additional images
- Staff photos
- User profile photos
- Job/Order assets (job_image_assets)
- DTF order design files
- Direct Image Printing order design files
- Order line item images
- Livewire SecureImageUpload component

## CDN Delivery

When Cloudinary credentials are present, all image URLs in the frontend resolve to:
`https://res.cloudinary.com/{cloud_name}/image/upload/...`

This applies to:
- Product pages (featured image, gallery)
- Blog posts (featured image, content images)
- Staff profiles (photos)
- Job order pages (job image assets)
- Admin dashboard (product thumbnails, staff photos)
- Invoices and receipts PDFs (using `MediaUrl::resolve`)
