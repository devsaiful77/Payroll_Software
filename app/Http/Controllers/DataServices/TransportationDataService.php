<?php

namespace App\Http\Controllers\DataServices;

use App\Models\DriverInfo;
use App\Models\DrivVehicleRecord;
use App\Models\VehicleUserPhotoVideo;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class TransportationDataService{


    public function getAllActiveVehicleInfoForDropdown(){

        $alredy_assinged_veh = $this->getAllVehicleIdThoseAreAlreadyAsignedDriver();
        return Vehicle::select('veh_id', 'veh_name')->where('status', 1)
              ->whereNotIn('veh_id',$alredy_assinged_veh)->orderBy('veh_id', 'ASC')->get();
    }

    public function getVehiclesWhichAreNotAssignedDriver(){

        $alredy_assinged_veh = $this->getAllVehicleIdThoseAreAlreadyAsignedDriver();
        return Vehicle::where('status', 1)
              ->whereNotIn('veh_id',$alredy_assinged_veh)->orderBy('veh_id', 'ASC')->get();
    }
    public function getVehiclesWhichAlreadyAssignedDriver(){

        $alredy_assinged_veh = $this->getAllVehicleIdThoseAreAlreadyAsignedDriver();
        return Vehicle::where('status', 1)
              ->whereIn('veh_id',$alredy_assinged_veh)->orderBy('veh_id', 'ASC')->get();
    }

    public function getAssignedOrNotAllActiveVehicleInfoForDropdown(){

         return Vehicle::select('veh_id', 'veh_name')->where('status', 1)
                          ->orderBy('veh_id', 'ASC')->get();
    }

     public function getAllActiveVehicaleInfos(){
        return Vehicle::
        leftjoin('project_infos', 'project_infos.proj_id', '=', 'vehicles.veh_proj_id')
        ->select(
            'project_infos.proj_name',
            'vehicles.*'
        ) // Add specific columns as required here
        ->where('vehicles.status', 1)
        ->orderBy('vehicles.veh_id', 'DESC')->get();
    }
    public function getSingleVehicleDetails($id){
        return Vehicle::where('status', 1)->where('veh_id', $id)->first();
    }

    public function checkThisVehicleAlreadyExist($veh_plate_number){
        return (Vehicle::where('veh_plate_number', $veh_plate_number)->count()) > 0 ? true:false;
    }

    public function insertVehicleInfoWithVehicleId($request){
        return Vehicle::insertGetId([
            'company_id'=> $request->company_id,
            'veh_name' => $request->veh_name,
            'veh_plate_number' => $request->veh_plate_number,
            'veh_model_number' => $request->veh_model_number,
            'veh_brand_name' => $request->veh_brand_name,
            'veh_licence_no' => $request->veh_licence_no,
            'veh_type_id' => $request->veh_type_id,
            'veh_insurrance_date' => $request->veh_insurrance_date,
            'veh_price' => $request->veh_price,
            'veh_present_metar' => $request->veh_present_metar,
            'veh_color' => $request->veh_color,
            'veh_purchase_date' => $request->veh_purchase_date,
            'veh_ins_expire_date' => $request->veh_ins_expire_date,
            'veh_ins_renew_date' => $request->veh_ins_renew_date,
            'veh_reg_expire_date' => $request->veh_reg_expire_date,
            'veh_reg_renew_date' => $request->veh_reg_renew_date,
            'remarks' => $request->remarks,
            'veh_owner_type' => $request->veh_owner_type,
            'veh_proj_id' => $request->veh_proj_id,
            'create_by_id' =>  Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);
    }

    // Vehicle Insurance Certificate feild Database operation
    public function updateVehicleInsuranceCertificateDbPath($veh_id, $filePath, $dbColoumName)
    {
        if ($filePath == '' || $filePath == null)
            return false;
        return  Vehicle::where('veh_id', $veh_id)->update([
            $dbColoumName => $filePath,
            'updated_at' => Carbon::now(),
        ]);
    }
    // Vehicle Registration Certificate feild Database operation
    public function updateVehicleRegistrationCertificateDbPath($veh_id, $filePath, $dbColoumName)
    {
        if ($filePath == '' || $filePath == null)
            return false;
        return  Vehicle::where('veh_id', $veh_id)->update([
            $dbColoumName => $filePath,
            'updated_at' => Carbon::now(),
        ]);
    }
    // Vehicle Photo feild Database operation
    public function updateVehiclePhotoDbPath($veh_id, $filePath, $dbColoumName)
    {
        if ($filePath == '' || $filePath == null)
            return false;
        return  Vehicle::where('veh_id', $veh_id)->update([
            $dbColoumName => $filePath,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateSingleVehicleRelatedInfo($request){

        return Vehicle::where('veh_id', $request->veh_id)->update([
            'company_id'=> $request->company_id,
            'veh_name' => $request->veh_name,
            'veh_plate_number' => $request->veh_plate_number,
            'veh_model_number' => $request->veh_model_number,
            'veh_brand_name' => $request->veh_brand_name,
            'veh_licence_no' => $request->veh_licence_no,
            'veh_type_id' => $request->veh_type_id,
            'veh_insurrance_date' => $request->veh_insurrance_date,
            'veh_price' => $request->veh_price,
            'veh_present_metar' => $request->veh_present_metar,
            'veh_color' => $request->veh_color,
            'veh_purchase_date' => $request->veh_purchase_date,
            'veh_ins_expire_date' => $request->veh_ins_expire_date,
            'veh_ins_renew_date' => $request->veh_ins_renew_date,
            'veh_reg_expire_date' => $request->veh_reg_expire_date,
            'veh_reg_renew_date' => $request->veh_reg_renew_date,
            'remarks' => $request->remarks,
            'status' => $request->active_status,
            'veh_owner_type' => $request->veh_owner_type,
            'veh_proj_id' => $request->veh_proj_id,
            'create_by_id' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function getSingleVehicleInfoWithVehIdForImageUpdate($id){
        return Vehicle::where('veh_id', $id)->where('status', 1)->first();
    }

    public function singleVehicleStatusInActive($id){
        return Vehicle::where('status', 1)->where('veh_id', $id)->update([
            'status' => 0,
            'updated_at' => Carbon::now(),
        ]);
    }

    // Get Vehicle Information for search with Plate Number
    public function searchForVehicleInformationWithPlateNumber($request){
        return Vehicle::where('veh_plate_number', $request->value)
        ->join('employee_infos', 'vehicles.driver_id', '=', 'employee_infos.emp_auto_id')
        ->firstOrFail();
    }


  /*
  |--------------------------------------------------------------------------
  |  Driver Info Related Database Operation
  |--------------------------------------------------------------------------
  */
    public function getAllActiveDriverInfoForDropdown(){

        $alredy_assinged_driver = $this->getAllDriverIdThoseAreAlreadyAsignedVehicles();
      return  DriverInfo::select('dri_auto_id', 'dri_name')->where('status', 1)
            ->whereNotIn('dri_auto_id',$alredy_assinged_driver)->orderBy('dri_auto_id', 'ASC')->get();

    }
    public function getAllActiveDriverInfos(){
        return DriverInfo::orderBy('dri_auto_id', 'DESC')->get();
    }
    public function getSingleDriverInfoWithDriverAutoID($id){
        return DriverInfo::where('dri_auto_id', $id)->first();
    }
    public function checkThisDriverAlreadyExist($driver_emp_id){
        return (DriverInfo::where('dri_emp_id', $driver_emp_id)->count()) > 0 ? true:false;
    }
    public function insertDriverInfoWithDriverId($request){
        return DriverInfo::insertGetId([
            'dri_emp_id' => $request->dri_empId,
            'dri_iqama_no' => $request->dri_iqamaNo,
            'dri_name' => $request->dri_name,
            'dri_address' => $request->present_address,
            'dri_license_type_id' => $request->dri_license_type_id,
            'insert_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);
    }
    // Driver Iqama Certificate Upload into Database
    public function updateDriverIqamaCertificateDbPath($dri_id, $filePath, $dbColoumName)
    {
        if ($filePath == '' || $filePath == null)
            return false;
        return  DriverInfo::where('dri_auto_id', $dri_id)->update([
            $dbColoumName => $filePath,
            'updated_at' => Carbon::now(),
        ]);
    }
    // Driver Driving Licese Upload into Database
    public function updateDrivingLicenseCertificateDbPath($dri_id, $filePath, $dbColoumName)
    {
        if ($filePath == '' || $filePath == null)
            return false;
        return  DriverInfo::where('dri_auto_id', $dri_id)->update([
            $dbColoumName => $filePath,
            'updated_at' => Carbon::now(),
        ]);
    }
    // Driver Insurance Certificate Upload into Database
    public function updateDriverInsuranceCertificateDbPath($dri_id, $filePath, $dbColoumName)
    {
        if ($filePath == '' || $filePath == null)
            return false;
        return  DriverInfo::where('dri_auto_id', $dri_id)->update([
            $dbColoumName => $filePath,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateSingleDriverInfos($request){
        $id = $request->dri_auto_id;
        $updatedBy = Auth::user()->id;

        return DriverInfo::where('dri_auto_id', $id)->update([
            'dri_emp_id' => $request->dri_empId,
            'dri_iqama_no' => $request->dri_iqamaNo,
            'dri_name' => $request->dri_name,
            'dri_address' => $request->present_address,
            'dri_license_type_id' => $request->dri_license_type_id,
            'updated_by' => $updatedBy,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function getSingleDriverInfoWithDriverIdForImageUpdate($id){
        return DriverInfo::where('dri_auto_id', $id)->first();
    }

    public function singleDriverDriverInfoDeactive($drivId){
        return DriverInfo::where('dri_auto_id', $drivId)->update([
            'status' => 0,
            'updated_at' => Carbon::now(),
        ]);
    }
    public function updateDriverActiveInactiveStatus($driver_auto_id, $status,$updated_by){
        return DriverInfo::where('dri_auto_id', $driver_auto_id)->update([
            'status' => $status,
            'updated_at' => Carbon::now(),
            'updated_by' =>$updated_by,

        ]);
    }

    public function getDriverDetailsInformationByEmployeeInformation($driEmpID){
        return DriverInfo::where('dri_emp_id', $driEmpID)->first();
    }

  /*
  |--------------------------------------------------------------------------
  | Vehicle Drivers Info Related Database Operation
  |--------------------------------------------------------------------------
  */



  public function getAllDriverIdThoseAreAlreadyAsignedVehicles(){
    return DrivVehicleRecord::select('driv_auto_id')->where('status', 1)->get();
  }

  public function getAllVehicleIdThoseAreAlreadyAsignedDriver(){
    return DrivVehicleRecord::select('veh_auto_id')->where('status', 1)->get();
  }

  public function getAllActiveDriverVehicleInfos(){
    return DrivVehicleRecord::orderBy('driv_veh_auto_id', 'DESC')->get();
  }

  public function getADriverDrivingVehicleInformation($driver_auto_id){
    return DrivVehicleRecord::where('driv_auto_id', $driver_auto_id)
        ->leftJoin('vehicles', 'vehicles.veh_id', '=', 'driv_vehicle_records.veh_auto_id')
        ->select(
            'vehicles.veh_name',
            'vehicles.veh_plate_number',
            'vehicles.veh_model_number',
            'vehicles.veh_color',
            'vehicles.veh_licence_no',
            'driv_vehicle_records.*'
        ) // Add specific columns as required here
        ->where('driv_vehicle_records.status', 0)
        ->first();

  }
  public function getSingleDriverVehicleInfosWithDriverVehicleAutoIDForEdit($id){
    return DrivVehicleRecord::where('driv_veh_auto_id', $id)->first();
  }

  public function checkThisVehicleDriverRecordsIsExist($driv_id){
    $drivRecord = DrivVehicleRecord::where('driv_auto_id', $driv_id)->orWhere('release_date', null)->where('status', 0)->first();
    if ($drivRecord != null) {
        return true;
    } else {
        return false;
    }
  }
  public function checkThisVehicleIsAvailableToAssignNewDriver($veh_id){

    $no_of_record = DrivVehicleRecord::where('veh_auto_id', $veh_id)->where('status', 1)->count();
     return $no_of_record > 0 ? false : true ;
  }

  public function checkThisDriverIsAvailableToAssignNewVehicle($driv_id){

     $no_of_record = DrivVehicleRecord::where('driv_auto_id', $driv_id)->where('status', 1)->count();
     return $no_of_record > 0 ? false : true ;
  }


  public function insertVehicleDriverRecordsInfos($request){

    if ($this->checkThisDriverIsAvailableToAssignNewVehicle($request->driv_id) == false) {
        return 0;
    }
    else if ($this->checkThisVehicleIsAvailableToAssignNewDriver($request->veh_id) == false) {
        return 0;
    } else {
        return DrivVehicleRecord::insert([
            'driv_auto_id' => $request->driv_id,
            'veh_auto_id' => $request->veh_id,
            'assign_date' => $request->assigned_date,
            'project_id' => $request->project_id,
            'insert_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);
    }

  }
  public function driverDataInActiveInDriverTable($driv_auto_id){
    return DriverInfo::where('dri_auto_id', $driv_auto_id)->update([
        'status' => 0,
    ]);
  }

  public function driverDataActiveInDriverTable($driv_veh_auto_id){
    $drivVehInfo = DrivVehicleRecord::where('driv_veh_auto_id', $driv_veh_auto_id)->first();
    $drivID = $drivVehInfo->driv_auto_id;
    return DriverInfo::where('dri_auto_id', $drivID)->update([
        'status' => 1,
    ]);
  }

  public function singleDriverVehicleInfosUpdate($request){

    if ($this->checkThisDriverIsAvailableToAssignNewVehicle($request->driv_id) == false) {
        return 0;
    }
    else if ($this->checkThisVehicleIsAvailableToAssignNewDriver($request->veh_id) == false) {
        return 0;
    }else {

        return DrivVehicleRecord::where('driv_veh_auto_id', $request->driv_veh_auto_id)->update([
            'driv_auto_id' => $request->driv_id,
            'veh_auto_id' => $request->veh_id,
            'assign_date' => $request->assigned_date,
            'update_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
     }

  }

  public function singleDriverVehicleInfoDeActiveWithReleasedDate($id){
        return DrivVehicleRecord::where('driv_veh_auto_id', $id)->update([
            'status' => 0,
            'update_by' => Auth::user()->id,
            'release_date' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
  }

    //   Find Vehicle Name Wise Drivers Info
public function getAllDriverInfoWithSpecificVehicle($veh_id){
    return DrivVehicleRecord::where('veh_auto_id', $veh_id)
    ->leftjoin('driver_infos', 'driv_vehicle_records.driv_auto_id', '=', 'driver_infos.dri_auto_id')
    ->get();
}

public function saveVehicleDriverPhotoVideo($driver_auto_id,$veh_auto_id,$file_type,$photo_path,$video_url,$remarks){


        return VehicleUserPhotoVideo::insert([
            'driv_auto_id' => $driver_auto_id,
            'veh_auto_id' => $veh_auto_id,
            'video_url' => $video_url,
            'photos'=>$photo_path,
            'remarks'=>$remarks,
            'inserted_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);

}





  // Get Company And Vehicle Type Wise All Active Vehicle Information for Report
  public function getCompanyNameAndVehicleTypeWiseAllVehicleRecords($company_id, $veh_type_id){

        if ($company_id == null && $veh_type_id == null)
        {
            return Vehicle:: leftjoin('driv_vehicle_records', 'vehicles.veh_id' ,'=', 'driv_vehicle_records.veh_auto_id')
            ->leftjoin('driver_infos', 'driv_vehicle_records.driv_auto_id', '=', 'driver_infos.dri_auto_id')
            ->orderBy('veh_id', 'ASC')->get();
        }
        elseif ($company_id == null && $veh_type_id != null)
        {

            return Vehicle:: leftjoin('driv_vehicle_records', 'vehicles.veh_id' ,'=', 'driv_vehicle_records.veh_auto_id')
            ->leftjoin('driver_infos', 'driv_vehicle_records.driv_auto_id', '=', 'driver_infos.dri_auto_id')
            ->where('veh_type_id', $veh_type_id)
            ->orderBy('veh_id', 'ASC')->get();

        }
        elseif ($company_id != null && $veh_type_id == null)
        {
            return Vehicle:: leftjoin('driv_vehicle_records', 'vehicles.veh_id' ,'=', 'driv_vehicle_records.veh_auto_id')
                        ->leftjoin('driver_infos', 'driv_vehicle_records.driv_auto_id', '=', 'driver_infos.dri_auto_id')
                        ->where('company_id', $company_id)
                        ->orderBy('veh_id', 'ASC')->get();
        }
        elseif ($company_id != null && $veh_type_id != null)
        {
            return Vehicle:: leftjoin('driv_vehicle_records', 'vehicles.veh_id' ,'=', 'driv_vehicle_records.veh_auto_id')
            ->leftjoin('driver_infos', 'driv_vehicle_records.driv_auto_id', '=', 'driver_infos.dri_auto_id')
                        ->where('veh_type_id', $veh_type_id)
                        ->where('company_id', $company_id)
                        ->orderBy('veh_id', 'ASC')->get();
        }

  }

  public function getVehicleDetailsInformationByProjectID($project_id)
  {
      return Vehicle::leftJoin('project_infos', 'project_infos.proj_id', '=', 'vehicles.veh_proj_id')
          ->select(
              'project_infos.proj_name',
              'vehicles.*'
          )
          ->whereIn('vehicles.veh_proj_id', $project_id)
          ->orderBy('vehicles.veh_id', 'DESC')->get();
  }

  public function getAllVehicleDetailsInformation()
  {
      return Vehicle::leftJoin('project_infos', 'project_infos.proj_id', '=', 'vehicles.veh_proj_id')
          ->select(
              'project_infos.proj_name',
              'vehicles.*'
          )
          ->orderBy('vehicles.veh_id', 'DESC')->get();
  }



    public function getProjectWiseVehicleDetailsInformation(){

    }

    public function searchAVehicleDetailsInformation($vehicle_plat_number){
        return Vehicle::where('vehicles.veh_plate_number',$vehicle_plat_number)->orderBy('veh_id', 'ASC')->first();
    }

    public function searchAVehicleDriverAssignAllRecords($vehicle_auto_id){

        return DrivVehicleRecord::where('veh_auto_id',$vehicle_auto_id)
                ->leftjoin('driver_infos', 'driv_vehicle_records.driv_auto_id', '=', 'driver_infos.dri_auto_id')
                ->leftjoin('users', 'driv_vehicle_records.insert_by', '=', 'users.id')
                ->leftjoin('employee_infos', 'driver_infos.dri_emp_id', '=', 'employee_infos.employee_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->orderBy('driv_veh_auto_id', 'DESC')->get();
    }

    public function getListOfVehicleDetailsWithDriverInformationByProjectID($project_id_list)
    {
        // return Vehicle::leftJoin('project_infos', 'project_infos.proj_id', '=', 'vehicles.veh_proj_id')
        //     ->select(
        //         'project_infos.proj_name',
        //         'vehicles.*'
        //     )
        //     ->whereIn('vehicles.veh_proj_id', $project_id)
        //     ->orderBy('vehicles.veh_id', 'DESC')->get();


            return DrivVehicleRecord::whereIn('driv_vehicle_records.project_id', $project_id_list)
            ->where('driv_vehicle_records.status',1)
            ->leftjoin('driver_infos', 'driv_vehicle_records.driv_auto_id', '=', 'driver_infos.dri_auto_id')
            ->leftjoin('vehicles', 'driv_vehicle_records.driv_auto_id', '=', 'vehicles.veh_id')
            ->leftjoin('project_infos', 'driv_vehicle_records.project_id', '=', 'project_infos.proj_id')
            ->orderBy('driv_veh_auto_id', 'DESC')->get();
    }





}
