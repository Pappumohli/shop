
///single record ko fetch karne ke liye query use kiye he 
 @POST("fetchsingleuser.php?id=")
Call<GetResponse>fetchsingle(@Query("id")String ids);





public class MainActivity extends AppCompatActivity {

AppCompatButton buttonw;
   String imagePath;
   private CharSequence[] options = {"Camera", "Gallery", "Cancel"};
   private String selectedImage;
   private final int CAMERA_RE_CODE = 1;
   ProgressDialog dialog;
   //ApiInterface apiInterface;
   private Bitmap bitmap;
   AppCompatButton button;
   TextInputEditText name, email, password, address;
   ImageView Image, selectimage;
   ActivityResultLauncher<Intent> galleryLouncher;

   @Override
   protected void onCreate(Bundle savedInstanceState) {
       super.onCreate(savedInstanceState);
       setContentView(R.layout.activity_main);

       dialog = new ProgressDialog(MainActivity.this);
       dialog.setCancelable(true);
       dialog.setCanceledOnTouchOutside(true);
       dialog.setMessage("Please Wait...");
       dialog.setTitle("Signup Now");
       dialog.setProgressStyle(ProgressDialog.STYLE_SPINNER);
buttonw = findViewById(R.id.other);

       //image
       selectimage = findViewById(R.id.selectImage);
       Image = findViewById(R.id.selectImage);


       button = findViewById(R.id.signup);
       name = findViewById(R.id.username);
       password = findViewById(R.id.password);
       email = findViewById(R.id.email);
       selectimage = findViewById(R.id.imageView);
       address = findViewById(R.id.address);

       buttonw.setOnClickListener(new View.OnClickListener() {
           @Override
           public void onClick(View view) {
               Intent intent = new Intent(MainActivity.this, ShowActivity.class);
               startActivity(intent);
           }
       });


       selectimage.setOnClickListener(new View.OnClickListener() {
           @Override
           public void onClick(View view) {
               AlertDialog.Builder builder = new AlertDialog.Builder(MainActivity.this);
               builder.setTitle("select Image");
               builder.setItems(options, new DialogInterface.OnClickListener() {
                   @Override
                   public void onClick(DialogInterface dialogInterface, int i) {
                       if (options[i].equals("Camera")) {


                           Intent takePic = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);


                           startActivityForResult(takePic, CAMERA_RE_CODE);
                       } else if (options[i].equals("Gallery")) {

                           Intent intent = new Intent(Intent.ACTION_PICK);
                           intent.setData(MediaStore.Images.Media.EXTERNAL_CONTENT_URI);

                           galleryLouncher.launch(intent);

                       } else {

                           // dialog.dismiss();

                       }

                   }
               });
               builder.show();
           }
       });





       galleryLouncher = registerForActivityResult(new ActivityResultContracts.StartActivityForResult(), new ActivityResultCallback<ActivityResult>() {
           @Override
           public void onActivityResult(ActivityResult result) {

               if (result.getResultCode() == Activity.RESULT_OK) {
                   Intent data = result.getData();
                   Uri uri = data.getData();

                   imagePath = getRealPathFromUrl(uri);
                   try {
                       Bitmap bitmap = MediaStore.Images.Media.getBitmap(getContentResolver(), uri);

                       Image.setImageBitmap(bitmap);
                   } catch (IOException e) {
                       e.printStackTrace();
                   }
               }

           }
       });



       button.setOnClickListener(new View.OnClickListener() {
           @Override
           public void onClick(View view) {

               String username = name.getText().toString().trim();
               String userrmail = email.getText().toString().trim();
               String userpass = password.getText().toString().trim();
               String uadress = address.getText().toString().trim();
               //  String uimage = imageToString();


               if (username.equals("")) {
                   name.requestFocus();
                   name.setError("enter name");
                   return;
               }

               if (userrmail.equals("")) {
                   email.requestFocus();
                   email.setError("enter nemail");
                   return;
               }


               if (!Patterns.EMAIL_ADDRESS.matcher(userrmail).matches()) {
                   email.requestFocus();
                   email.setError("enter right email");
                   return;
               }

               if (password.equals("")) {
                   password.requestFocus();
                   password.setError("enter nemail");
                   return;
               }

               if (password.length() < 4) {
                   password.requestFocus();
                   password.setError("enter 5 character pasword");
                   return;
               }

               if (address.equals("")) {
                   address.requestFocus();
                   address.setError("enter address");
                   return;
               }
               dialog.show();

               File file = new File(imagePath);
               RequestBody requestBody = RequestBody.create(MediaType.parse("multipart/form-data"),file);
               MultipartBody.Part body = createFormData("file",file.toString(),requestBody);


               MultipartBody.Part bodys = createFormData("username", username);
               MultipartBody.Part bodys2 = createFormData("email", userrmail);
               MultipartBody.Part bodys3 = createFormData("password", userpass);
               MultipartBody.Part bodys4 = createFormData("address", uadress);

               Call<SignupResponse> col = RetrofitClient.getInstance().getApi().insertdata(bodys,bodys2,bodys3,bodys4,body);


               col.enqueue(new Callback<SignupResponse>() {
                   @Override
                   public void onResponse(Call<SignupResponse> call, Response<SignupResponse> response) {
                       if (response.isSuccessful()) {

                           SignupResponse signupResponse = response.body();
                           if (response.body().getError().equals("000")) {
                               dialog.dismiss();
                               name.setText("");
                               email.setText("");
                               password.setText("");
                               address.setText("");
                               // assert signupResponse != null;
                               Toast.makeText(MainActivity.this, signupResponse.getMessage(), Toast.LENGTH_SHORT).show();
                           } else {
                               Toast.makeText(MainActivity.this, signupResponse.getMessage(), Toast.LENGTH_SHORT).show();
                           }


                       }
                   }

                   @Override
                   public void onFailure(Call<SignupResponse> call, Throwable t) {
                       Toast.makeText(MainActivity.this, t.getMessage(), Toast.LENGTH_SHORT).show();

                   }
               });




           }
       });

   }

   private String getRealPathFromUrl (Uri uri){
       String[] projection = {MediaStore.Images.Media.DATA};
       CursorLoader loder = new CursorLoader(getApplicationContext(), uri, projection, null, null, null);
       Cursor cursor = loder.loadInBackground();
       int column_idx = cursor.getColumnIndexOrThrow(MediaStore.Images.Media.DATA);
       cursor.moveToFirst();
       String result = cursor.getString(column_idx);
       cursor.close();
       return result;
   }


/// api ka 

public interface Api {


//
//@FormUrlEncoded
 @Multipart
 @POST("signup.php")
 Call<SignupResponse> insertdata(@Part MultipartBody.Part name,
                                 @Part MultipartBody.Part email,
                                 @Part MultipartBody.Part pass,
                                 @Part MultipartBody.Part address,
                                 @Part MultipartBody.Part image
                                 );



}


