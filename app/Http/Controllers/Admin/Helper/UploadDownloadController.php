<?php

namespace App\Http\Controllers\Admin\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;


class UploadDownloadController extends Controller
{


  public function uploadEmployeeProfilePhoto($file,$oldFilePath)
  {
    if($file == null)
    return null;
    $appoint_name = 'Emp-' . time() . '.' . $file->getClientOriginalExtension();
    $destinationPath = "uploads/employee/profile/";
    $uploadPath =  $destinationPath . $appoint_name;

    $file->move($destinationPath, $appoint_name);
    if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
    {  unlink($oldFilePath);}         // File::delete($oldFilePath);

    return $uploadPath;

  }

  public function uploadEmployeePassportFile($file,$oldFilePath)
  {
    if($file == null)
    return null;
    $appoint_name = 'Passport-' . time() . '.' . $file->getClientOriginalExtension();
    $destinationPath = "uploads/employee/passport/";
    $uploadPath =  $destinationPath . $appoint_name;
    $file->move($destinationPath, $appoint_name);
    if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
    {  unlink($oldFilePath);}

   return $uploadPath;

  }

  public function uploadEmployeeIqamaFile($file,$oldFilePath)
  {
    if($file == null)
    return null;
    $appoint_name = 'Iqama-' . time() . '.' . $file->getClientOriginalExtension();
    $destinationPath = "uploads/employee/iqama/";
    $uploadPath =  $destinationPath . $appoint_name;
    $file->move($destinationPath, $appoint_name);
    if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
    { unlink($oldFilePath);}         // File::delete($oldFilePath);

   return $uploadPath;

  }

  public function uploadEmployeeAppointmentLetterFile($file,$oldFilePath)
  {
    if($file == null)
    return null;
    $appoint_name = 'Appointment-' . time() . '.' . $file->getClientOriginalExtension();
    $destinationPath = "uploads/employee/appoinment/";
    $uploadPath =  $destinationPath . $appoint_name;
    $file->move($destinationPath, $appoint_name);
    if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
    {  unlink($oldFilePath);}         // File::delete($oldFilePath);

   return $uploadPath;
  }

  public function uploadEmployeeMedicalReportFile($file,$oldFilePath)
  {
    if($file == null)
    return null;
    $appoint_name = 'Medical-' . time() . '.' . $file->getClientOriginalExtension();
    $destinationPath = "uploads/employee/medical/";
    $uploadPath =  $destinationPath . $appoint_name;
    $file->move($destinationPath, $appoint_name);
    if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

   return $uploadPath;

  }

  public function uploadBDOfficePaymentSlip($file,$oldFilePath)
  {
    if($file == null)
    return null;
    $appoint_name = 'Payment-' . time() . '.' . $file->getClientOriginalExtension();
    $destinationPath = "uploads/payment_slip/";
    $uploadPath =  $destinationPath . $appoint_name;
    $file->move($destinationPath, $appoint_name);
    if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

   return $uploadPath;

  }

  public function uploadEmployeeCOVIDCertificateFile($file,$oldFilePath)
  {
      if($file == null)
      return null;
      $appoint_name = 'covid-' . time() . '.' . $file->getClientOriginalExtension();
      $destinationPath = "uploads/employee/covid/";
      $uploadPath =  $destinationPath . $appoint_name;
      $file->move($destinationPath, $appoint_name);
      if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
          {  unlink($oldFilePath);}

     return $uploadPath;

  }
  public function uploadEmployeeEducationalDocuments($file,$oldFilePath)
  {
      if($file == null)
      return null;
      $appoint_name = 'Edu-' . time() . '.' . $file->getClientOriginalExtension();
      $destinationPath = "uploads/employee/edu/";
      $uploadPath =  $destinationPath . $appoint_name;
      $file->move($destinationPath, $appoint_name);
      if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
          {  unlink($oldFilePath);}

     return $uploadPath;

  }



  /*
   ==========================================================================
   ============================= Employee Promotion Releted Documents =======
   ==========================================================================
  */
        //Promotion Approved Document
    public function uploadEmployeePromotionApprovedPhoto($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $prom_doc_name = 'apprv-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/employee/promotion/";
        $uploadPath =  $destinationPath . $prom_doc_name;
        $file->move($destinationPath, $prom_doc_name);
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
            {  unlink($oldFilePath);}

       return $uploadPath;
    }









    /*
     ==========================================================================
     ============================= Company Vehicle Documents ==================
     ==========================================================================
    */

    // (Insurance Certificate)
    public function uploadVehicleInsuranceCertificate($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $appoint_name = 'veh-ins-cert' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/vehicle/insurance/";
        $uploadPath =  $destinationPath . $appoint_name;
        $file->move($destinationPath, $appoint_name);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}         // File::delete($oldFilePath);

        return $uploadPath;

    }
    // (Registration Certificate)
    public function uploadVehicleRegistrationCertificate($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $appoint_name = 'veh-reg-cert' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/vehicle/";
        $uploadPath =  $destinationPath . $appoint_name;
        $file->move($destinationPath, $appoint_name);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}         // File::delete($oldFilePath);

        return $uploadPath;

    }
    // (Vehicle Photo)
    public function uploadVehiclePhoto($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $appoint_name = 'veh-photo' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/vehicle/";
        $uploadPath =  $destinationPath . $appoint_name;
        $file->move($destinationPath, $appoint_name);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}         // File::delete($oldFilePath);

        return $uploadPath;
    }
    /*
     ==========================================================================
     ============================= Company Driver Documents ============================
     ==========================================================================
    */
    // Driver Iqama Certificate
    public function uploadDriverIqamaCertificate($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $appoint_name = 'driv-iqama' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/vehicle/";
        $uploadPath =  $destinationPath . $appoint_name;
        $file->move($destinationPath, $appoint_name);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}         // File::delete($oldFilePath);

        return $uploadPath;
    }
    // Driver Driving License
    public function uploadDrivingLicenseCertificate($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $appoint_name = 'driv-licen' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/vehicle/";
        $uploadPath =  $destinationPath . $appoint_name;
        $file->move($destinationPath, $appoint_name);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}         // File::delete($oldFilePath);

        return $uploadPath;
    }
    // Driver Insurance Certificate
    public function uploadDriverInsuranceCertificate($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $appoint_name = 'driv-insur' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/vehicle/";
        $uploadPath =  $destinationPath . $appoint_name;
        $file->move($destinationPath, $appoint_name);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}         // File::delete($oldFilePath);

        return $uploadPath;
    }


    public function uploadDriverVehiclePhotos($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $appoint_name = 'dv' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/vehicle/";
        $uploadPath =  $destinationPath . $appoint_name;
        $file->move($destinationPath, $appoint_name);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}         // File::delete($oldFilePath);

        return $uploadPath;
    }



    /*
     ==========================================================================
         ================ Company Building Rent Documents ================
     ==========================================================================
    */
    public function uploadBuildingRentDeedPhoto($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $appoint_name = 'office_dead-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/office-building/";
        $uploadPath =  $destinationPath . $appoint_name;
        $file->move($destinationPath, $appoint_name);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}         // File::delete($oldFilePath);

        return $uploadPath;
    }




     /*
     ==========================================================================
         ================ uploadEmployeeSalarySheet ================
     ==========================================================================
    */
    public function uploadEmployeeSalarySheet($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $appoint_name = 'Salary' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/salarysheet/";
        $uploadPath =  $destinationPath . $appoint_name;
        $file->move($destinationPath, $appoint_name);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}         // File::delete($oldFilePath);

        return [$appoint_name,$uploadPath];
    }

    public function deleteUploadedSalarySheet($oldFilePath)
    {
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}         // File::delete($oldFilePath);


    }


    /*
     ==========================================================================
         ================ upload employee TUV Photo ================
     ==========================================================================
    */
    public function uploadEmployeeTUVPhoto($file,$oldFilePath)
    {

        if($file == null)
        return null;
        $appoint_name = 'Emp_TUV-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/employee/tuv_photo/";
        $uploadPath =  $destinationPath . $appoint_name;
        $file->move($destinationPath, $appoint_name);
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

       return $uploadPath;
    }





     /*
     ==========================================================================
         ================ upload employee Mobile Bill Paper Photo ================
     ==========================================================================
    */
    public function uploadEmployeeMobileBillPaper($file,$oldFilePath)
    {

        if($file == null)
        return null;
        $appoint_name = 'mb-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/mobile_bill/";
        $uploadPath =  $destinationPath . $appoint_name;
        $file->move($destinationPath, $appoint_name);
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

       return $uploadPath;
    }



     /*
     ==========================================================================
     =============================== uploadEmployeeSalarySheet ================
     ==========================================================================
    */
    public function uploadAdvancePaper($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $file_aname = 'Adv' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/advance/";
        $uploadPath =  $destinationPath . $file_aname;
        $file->move($destinationPath, $file_aname);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

        return $uploadPath;
    }

    public function deleteUploadedAdvancePaper($oldFilePath)
    {
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}
    }


    /*
     ==========================================================================
     =============================== Upload Item Recived Paper ================
     ==========================================================================
    */
    public function uploadItemReceivedPaper($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $file_aname = 'Item-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/Items/";
        $uploadPath =  $destinationPath . $file_aname;
        $file->move($destinationPath, $file_aname);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

        return $uploadPath;
    }

    public function deleteItemReceivedPaper($oldFilePath)
    {
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}
    }


    /*
     ==========================================================================
     =============================== Upload Invoice Paper ================
     ==========================================================================
    */
    public function uploadInvoiceSummaryPaper($file,$oldFilePath)
    {
       // dd($file);
        if($file == null)
        return null;

        $file_aname = 'inv-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/invoice/";
        $uploadPath =  $destinationPath . $file_aname;
        $file->move($destinationPath, $file_aname);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

        return $uploadPath;
    }

    public function deleteInvoiceSummaryUploadedPaper($oldFilePath)
    {
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}
    }


     /*
     ==========================================================================
     =============================== Upload Leave Paper =======================
     ==========================================================================
    */
    public function uploadEmployeeLeaveApplicationPaper($file,$oldFilePath)
    {
       // dd($file);
        if($file == null)
        return null;

        $file_aname = 'lev-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/Leave/";
        $uploadPath =  $destinationPath . $file_aname;
        $file->move($destinationPath, $file_aname);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

        return $uploadPath;
    }

    public function deleteEmployeeLeaveApplicationPaper($oldFilePath)
    {
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}
    }


  /*
     ==========================================================================
     =============================== Project Contract Paper====================
     ==========================================================================
    */
    public function uploadProjectContractPaper($file,$oldFilePath)
    {
       // dd($file);
        if($file == null)
        return null;

        $file_aname = 'Ag-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/project/";
        $uploadPath =  $destinationPath . $file_aname;
        $file->move($destinationPath, $file_aname);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

        return $uploadPath;
    }

    public function deleteProjectContractPaper($oldFilePath)
    {
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}
    }



    /*
     ==========================================================================
     =============================== Project Contract Paper====================
     ==========================================================================
    */
    public function uploadDrInvoiceFile($file,$oldFilePath)
    {

        if($file == null)
        return null;

        $file_aname = 'Dr-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/DrV/";
        $uploadPath =  $destinationPath . $file_aname;
        $file->move($destinationPath, $file_aname);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

        return $uploadPath;
    }

    public function deleteDrInvoiceFileFromPath($oldFilePath)
    {
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}
    }



}



