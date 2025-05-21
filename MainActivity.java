package com.example.sapieracameraapp;

import android.Manifest;
import android.content.ContentValues;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.provider.ContactsContract;
import android.provider.MediaStore;
import android.widget.ImageButton;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.core.content.FileProvider;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.text.SimpleDateFormat;
import java.util.Date;

public class MainActivity extends AppCompatActivity {
    private static final int CAMERA_PERM_CODE = 101;
    private static final int CAMERA_REQUEST_CODE = 102;
    private static final String FILE_PROVIDER_AUTHORITY = "com.example.sapieracameraapp.fileprovider";
    private String currentPhotoPath;
    private Uri imageUri;

    // ImageButtons
    private ImageButton cameraBtn, galleryBtn, messagesBtn, contactsBtn,
            facebookBtn, instagramBtn, youtubeBtn;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // Initialize ImageButtons
        initializeButtons();
        // Set click listeners
        setClickListeners();
    }

    private void initializeButtons() {
        cameraBtn = findViewById(R.id.cameraBtn);
        galleryBtn = findViewById(R.id.galleryBtn);
        messagesBtn = findViewById(R.id.messagesBtn);
        contactsBtn = findViewById(R.id.contactsBtn);
        facebookBtn = findViewById(R.id.facebookBtn);
        instagramBtn = findViewById(R.id.instagramBtn);
        youtubeBtn = findViewById(R.id.youtubeBtn);
    }

    private void setClickListeners() {
        cameraBtn.setOnClickListener(v -> askCameraPermissions());
        galleryBtn.setOnClickListener(v -> openGallery());
        messagesBtn.setOnClickListener(v -> openMessagingApp());
        contactsBtn.setOnClickListener(v -> openContacts());
        facebookBtn.setOnClickListener(v -> openFacebook());
        instagramBtn.setOnClickListener(v -> openInstagram());
        youtubeBtn.setOnClickListener(v -> openYouTube());
    }

    private void askCameraPermissions() {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(this,
                    new String[]{Manifest.permission.CAMERA},
                    CAMERA_PERM_CODE);
        } else {
            openCamera();
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions,
                                           @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == CAMERA_PERM_CODE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                openCamera();
            } else {
                Toast.makeText(this, "Camera Permission is Required to Use the Camera.",
                        Toast.LENGTH_SHORT).show();
            }
        }
    }

    private void openCamera() {
        Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        if (cameraIntent.resolveActivity(getPackageManager()) != null) {
            File photoFile = null;
            try {
                photoFile = createImageFile();
            } catch (IOException ex) {
                Toast.makeText(this, "Error occurred while creating the file.", Toast.LENGTH_SHORT).show();
                return;
            }

            if (photoFile != null) {
                try {
                    imageUri = FileProvider.getUriForFile(this, getPackageName() + ".fileprovider", photoFile);
                    cameraIntent.putExtra(MediaStore.EXTRA_OUTPUT, imageUri);
                    cameraIntent.addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION);
                    startActivityForResult(cameraIntent, CAMERA_REQUEST_CODE);
                } catch (IllegalArgumentException e) {
                    Toast.makeText(this, "Error accessing file provider: " + e.getMessage(), Toast.LENGTH_SHORT).show();
                }
            }
        } else {
            Toast.makeText(this, "No camera app found on device", Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @NonNull Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == CAMERA_REQUEST_CODE && resultCode == RESULT_OK) {
            try {
                saveImageToGallery();
                Toast.makeText(this, "Photo saved to gallery.", Toast.LENGTH_SHORT).show();
            } catch (Exception e) {
                Toast.makeText(this, "Error saving photo to gallery: " + e.getMessage(), Toast.LENGTH_SHORT).show();
            }
        } else if (resultCode == RESULT_CANCELED) {
            Toast.makeText(this, "Camera operation cancelled", Toast.LENGTH_SHORT).show();
        }
    }

    private File createImageFile() throws IOException {
        String timeStamp = new SimpleDateFormat("yyyyMMdd_HHmmss").format(new Date());
        String imageFileName = "JPEG_" + timeStamp + "_";
        File storageDir = getExternalFilesDir(Environment.DIRECTORY_PICTURES);
        if (storageDir == null) {
            throw new IOException("Failed to get external storage directory");
        }
        File image = File.createTempFile(
                imageFileName,
                ".jpg",
                storageDir
        );
        currentPhotoPath = image.getAbsolutePath();
        return image;
    }

    private void saveImageToGallery() throws IOException {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            ContentValues values = new ContentValues();
            values.put(MediaStore.Images.Media.DISPLAY_NAME, "IMG_" + System.currentTimeMillis());
            values.put(MediaStore.Images.Media.MIME_TYPE, "image/jpeg");
            values.put(MediaStore.Images.Media.RELATIVE_PATH, "Pictures/CameraApp");
            values.put(MediaStore.Images.Media.IS_PENDING, 1);

            Uri collection = MediaStore.Images.Media.getContentUri(MediaStore.VOLUME_EXTERNAL_PRIMARY);
            Uri imageUri = getContentResolver().insert(collection, values);
            if (imageUri == null) {
                throw new IOException("Failed to create new MediaStore record.");
            }

            try (OutputStream out = getContentResolver().openOutputStream(imageUri)) {
                if (out == null) {
                    throw new IOException("Failed to open output stream.");
                }
                File file = new File(currentPhotoPath);
                try (FileInputStream fileInput = new FileInputStream(file)) {
                    byte[] buffer = new byte[1024];
                    int length;
                    while ((length = fileInput.read(buffer)) > 0) {
                        out.write(buffer, 0, length);
                    }
                }
                values.put(MediaStore.Images.Media.IS_PENDING, 0);
                getContentResolver().update(imageUri, values, null, null);
            }
        } else {
            Intent mediaScanIntent = new Intent(Intent.ACTION_MEDIA_SCANNER_SCAN_FILE);
            File file = new File(currentPhotoPath);
            Uri contentUri = Uri.fromFile(file);
            mediaScanIntent.setData(contentUri);
            sendBroadcast(mediaScanIntent);
        }
    }

    private void openGallery() {
        try {
            Intent galleryIntent = new Intent(Intent.ACTION_PICK);
            galleryIntent.setType("image/*");
            galleryIntent.putExtra(Intent.EXTRA_MIME_TYPES, new String[]{"image/jpeg", "image/png"});
            galleryIntent.putExtra(Intent.EXTRA_LOCAL_ONLY, true);
            galleryIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            galleryIntent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            startActivity(galleryIntent);
        } catch (Exception e) {
            Toast.makeText(this, "Error opening gallery: " + e.getMessage(), Toast.LENGTH_SHORT).show();
        }
    }

    // Social media app methods
    private void openFacebook() {
        try {
            Intent intent = new Intent(Intent.ACTION_VIEW,
                    Uri.parse("fb://facewebmodal/f?href=https://www.facebook.com"));
            startActivity(intent);
        } catch (Exception e) {
            Toast.makeText(this, "Facebook app not installed. Opening in browser.", Toast.LENGTH_SHORT).show();
            Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse("https://www.facebook.com"));
            startActivity(browserIntent);
        }
    }

    private void openInstagram() {
        try {
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse("http://instagram.com/"));
            intent.setPackage("com.instagram.android");
            startActivity(intent);
        } catch (Exception e) {
            Toast.makeText(this, "Instagram app not installed. Opening in browser.", Toast.LENGTH_SHORT).show();
            Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse("https://www.instagram.com"));
            startActivity(browserIntent);
        }
    }

    private void openYouTube() {
        try {
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse("vnd.youtube://"));
            startActivity(intent);
        } catch (Exception e) {
            Toast.makeText(this, "YouTube app not installed. Opening in browser.", Toast.LENGTH_SHORT).show();
            Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse("https://www.youtube.com"));
            startActivity(browserIntent);
        }
    }

    private void openMessagingApp() {
        try {
            Intent intent = new Intent(Intent.ACTION_MAIN);
            intent.addCategory(Intent.CATEGORY_APP_MESSAGING);
            startActivity(intent);
        } catch (Exception e) {
            Toast.makeText(this, "Unable to open Messages.", Toast.LENGTH_SHORT).show();
        }
    }

    private void openContacts() {
        try {
            Intent intent = new Intent(Intent.ACTION_VIEW, ContactsContract.Contacts.CONTENT_URI);
            startActivity(intent);
        } catch (Exception e) {
            Toast.makeText(this, "Unable to open Contacts.", Toast.LENGTH_SHORT).show();
        }
    }
}
