# No Album Feature Implementation

## Changes Made:

### Backend Changes:

1. **DatabaseMySql.php**: Added `noalbum` filter case in `buildQuery()` method
   - Filters photos that are NOT in any album using SQL subquery
   - Added `getNoAlbumPhotoCount()` method to count photos without albums

2. **Album.php**: Added `getNoAlbumCount()` method
   - Returns count of photos not in any album for a user

3. **ApiAlbumController.php**: 
   - Modified `list_()` method to include virtual "No Album" entry
   - Modified `view()` method to handle "noalbum" special case
   - Virtual album has id="noalbum" and contains metadata

4. **PhotoController.php**: 
   - Added logic to detect "noalbum" filter
   - Added handling for "noalbum" case in photo list processing

### Frontend Changes:

5. **underscore.php**: Updated album template
   - Special handling for "noalbum" ID
   - Different link URL (/photos/noalbum/list)
   - Different styling for no-album cover
   - Disabled edit/delete actions for virtual album

## Features:

- **Smart Folder Behavior**: "No Album" appears only when there are photos without albums
- **Count Display**: Shows exact count of unorganized photos
- **Visual Distinction**: Special styling for the virtual album
- **Browse & Manage**: Users can browse photos and add them to albums
- **Non-Destructive**: Virtual album, can't be deleted or edited

## Usage:

1. Upload photos without assigning to albums
2. Visit /albums/list - "No Album" will appear at the top
3. Click "No Album" to view all unorganized photos
4. Use batch edit to assign photos to real albums

## Benefits:

- **Discovery**: Users can easily find unorganized photos
- **Organization**: Encourages proper photo management
- **Cleanup**: Helps identify photos that need categorization
- **Smart UI**: Only appears when needed