<?php

namespace App\Http\Controllers;

use App\Mail\TempPasswordMail;
use App\Models\City;
use App\Models\countries;
use App\Models\Course;
use App\Models\ImageTable;
use App\Models\SalesOrder;
use App\Models\SalesTest;
use App\Models\Social;
use App\Models\Student;
use App\Models\TestTraffic;
use App\Models\TrafficData;
use App\Models\User;
use Cache;
use DB;
use Hash;
use Illuminate\Http\Request;
use Log;
use Mail;
use Str;
use Validator;

class Sales extends Controller
{

    public function newapi()
    {
        return response()->json([
            'message' => 'Hello World!',
        ]);
    }
    public function index()
    {

        $sales = SalesTest::all();
        $firstssale = SalesTest::first();

        $searchsale = Social::with('trafficData')->get();



        $salesasad = Social::with('salesTest')->get();


        $testsearchsale = $searchsale->first();

        $asad = $asad = Student::with('course')->get();

        //     foreach ($asad as $as){


        //   echo $as->course_id;

        //     }





        //get single coloumn form db and then getting single coloumn value from that coloumn and assigning to variable
        $social = Social::first();
        $facebook = $social->facebook;
        $twitter = $social->twitter;
        $instagram = $social->instagram;
        $youtube = $social->youtube;


        $trafficData = TrafficData::first();
        $unionads = $trafficData->unionads;
        $videoads = $trafficData->videoads;
        $searchengine = $trafficData->searchengine;
        $direct = $trafficData->direct;
        $email = $trafficData->email;

        //yaha ham ek coloum ko pluck kar rahe aur iske sari values ko array me convert kar rahe hai

        $salesData = $sales->pluck('sales')->toArray();
        $revenueData = $sales->pluck('revenue')->toArray();
        $customerdata = $sales->pluck('customers')->toArray();
        $date = $sales->pluck('created_at')->toArray();

        $topRecords = DB::table('SalesOrder')
            ->orderBy('GrossProfit', 'desc')
            ->whereYear('CreatedOn', '2024')
            ->limit(10)
            ->get();



        foreach ($topRecords as $topRecord) {
            echo ' Total Profit =  ' . $topRecord->GrossProfit . ' By Sales Rep = ' . $topRecord->SalesRepID . ' Dated On ' . $topRecord->CreatedOn . '<br>';
        }

        return view('welcome', [
            'firstssale' => $firstssale,
            'sales' => $salesData,
            'revenue' => $revenueData,
            'customer' => $customerdata,
            'dates' => $date,
            'trafficData' => $trafficData,
            'unionads' => $unionads,
            'videoads' => $videoads,
            'searchengine' => $searchengine,
            'direct' => $direct,
            'email' => $email,
            'facebook' => $facebook,
            'twitter' => $twitter,
            'instagram' => $instagram,
            'youtube' => $youtube,
            'newsearchengine' => $testsearchsale->trafficData->searchengine,
            'test' => $testsearchsale->trafficData->email,
        ]);

    }

    public function image(Request $request)
    {

        return view('test');
    }

    public function sendmail(Request $request)
    {

        $email = $request->input('email');

        $otp = rand(10, 1000);



        if ($email) {

            Cache::put('otp', $otp, 3000);
            Mail::to($email)->send(new TempPasswordMail($otp));

            return response()->json([
                'status' => 'success',
                'message' => 'Email sent successfully.'
            ]);

        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not found.'
            ]);
        }
    }

    public function verifyotp(Request $request)
    {
        $otp = $request->input('otp');

        $storedotp = Cache::get('otp');

        if ($otp == $storedotp) {

            return response()->json([
                'status' => 'success',
                'message' => 'OTP verified successfully.'
            ]);


        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP not verified.'
            ]);
        }
    }


    public function methodsearch(Request $request)
    {
        $searchname = $request->input('searchname', 'no name');

        $search = Student::with('city')->whereHas('city', function ($query) use ($searchname) {
            $query->where('city_name', 'LIKE', '%' . $searchname . '%');
        })->get();

        return view('test', [
            'search' => $search,
        ]);
    }

    public function checking(Request $request)
    {
        // Validate the request
        $request->validate([
            'imagename' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Example validation rules
        ]);

        if ($request->hasFile('imagename')) {
            $image = $request->file('imagename');

            // Create a unique name for the image
            $newimagename = time() . '.' . $image->getClientOriginalExtension();

            // Move the uploaded file to the new location
            $image->move(public_path('/newimages'), $newimagename);

            // Create a new instance of the ImageTable model and save the image name
            $myimage = new ImageTable();
            $myimage->imagename = $newimagename;

            // Attempt to save the image
            try {
                $myimage->save();
                return redirect()->back()->with('success', 'Image uploaded successfully.');
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Image could not be saved. Please try again.',
                ], 500); // Return a 500 Internal Server Error status code
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No image file was uploaded.',
            ]);
        }
    }


    public function showsales()
    {

        $sales = SalesOrder::select('SalespersonName')->limit(10)->get();

        return view('sales', [
            'sales' => $sales
        ]);

    }

    public function createsale(Request $request)
    {



        $validator = Validator::make($request->all(), [
            'refno' => 'required|integer',
            'branchid' => 'required',
            'billto' => 'required',
            'billtoaddress' => 'required',
            'billtocity' => 'required',
            'billtostate' => 'required',
            'requestdelivery' => 'required',
            'salespersonname' => 'required',
            'notes' => 'required',

        ]);



        if ($validator->passes()) {

            $newsale = new SalesOrder();

            $newsale->RefNo = $request->refno;
            $newsale->BranchID = $request->branchid;
            $newsale->BillToAttn = $request->billto;
            $newsale->BillToAddress = $request->billtoaddress;
            $newsale->BillToCity = $request->billtocity;
            $newsale->BillToState = $request->billtostate;
            $newsale->BillToZip = $request->zipsold;
            $newsale->ShipToAttn = $request->shipto;
            $newsale->ShipToAddress = $request->shiptoaddress;
            $newsale->ShipToCity = $request->shiptocity;
            $newsale->DeliveryDate = $request->requestdelivery;
            $newsale->BranchID = $request->branchid;
            $newsale->SalespersonName = $request->salespersonname;
            $newsale->Notes = $request->notes;
            $newsale->SaleRevenue = $request->revenue;
            $newsale->GrossProfit = $request->profit;
            $newsale->TotalCommission = $request->commission;
            $newsale->save();


            \Log::info($newsale->save());


            return redirect()->back()->with('success', 'Sale created successfully.');

        } else {
            return redirect()->back()->withErrors($validator);
        }

    }

    public function showcharts(Request $request)
    {

        $employees = DB::table('employees')->join('employeeroles', 'employeeroles.AgentID', '=', 'employees.AgentID')
            ->join('roles', 'roles.RoleID', '=', 'employeeroles.RoleID')
            ->select('employees.AgentID', 'employees.FirstName', 'employees.LastName', 'roles.RoleName')->get();

        dd($employees);

        // $totalsales = DB::table('customers')->join('orders', 'customers.customer_id', '=', 'orders.customer_id')
        //     ->join('products', 'products.product_id', '=', 'orders.product_id')
        //     ->select('customers.name', 'customers.customer_id', 'orders.order_date', 'products.product_name')->where('orders.order_id', 2)->get();

        // $customersorders = DB::table('customers')->leftJoin('orders', 'customers.customer_id', '=', 'orders.customer_id')
        //     ->where('orders.order_id', 16)->get()->toArray();

        // if (empty($customersorders)){

        //     echo 'No Data Found';


        // innerjoin jab extaclty match karta hai tabhi data show karta hai
        // leftjoin jab match nahi hota tab bhi data show karta hai aur yeh null values check karne kaliye use hota hai yeh sari 
        // values ko show karta hai jo match nahi hoti


        // }
        // $orderCountPerCustomer = DB::table('customers')->join('orders', 'customers.customer_id', '=', 'orders.customer_id')
        //     ->join('products', 'products.product_id', '=', 'orders.product_id')
        //     ->select('customers.name', DB::raw('COUNT(orders.product_id) AS TotalProductOrders'))
        //     ->groupBy('customers.name')->get();


        // $newquerry = DB::table('customers')->join('orders', 'customers.customer_id', '=', 'orders.customer_id')
        //     ->select('customers.name', 'orders.order_date')->whereMonth('orders.order_date', '12')->get();

        // $myqueery = DB::table('customers')->leftJoin('orders', 'customers.customer_id', '=', 'orders.customer_id')
        //     ->leftJoin('products', 'products.product_id', '=', 'orders.product_id')
        //     ->select('customers.name', 'products.product_name')->
        //     where(function ($query) {
        //         $query->where('orders.order_id', 2)
        //             ->orWhere('orders.order_id', 16);
        //     })->get();

        // $dquerry = DB::table('customers')->join('orders', 'customers.customer_id', '=', 'orders.customer_id')
        //     ->select('customers.name', DB::raw('COUNT(orders.order_id) AS TotalOrders'))->groupBy('customers.name')->having('TotalOrders', '>', 5)->get();

        //



        // dd($totalsales);
        // dd($customersorders);
        //

        // foreach ($totalsale1 as $index => $total) {

        //    if($index<10){
        //     $sum10 += $total;
        //    }

        //    else{
        //     break;
        //    }



        // }



        // $example = DB::table('SalesOrder')->select('GrossProfit')->pluck('GrossProfit')->toArray();

        // // dd(

        // //     array_sum($example)
        // // // );
        // $saless = DB::table('SalesOrder')
        // ->select('GrossProfit', 'SaleRevenue', 'TotalCommission', 'SalespersonName', 'Price')
        // ->whereYear('CreatedOn', '2021')
        // ->where('SaleRevenue', '>', 5000)
        // ->where('TotalCommission', '<', 140)
        // ->limit(20)
        // ->get()
        // ->toArray();

        // foreach($saless as $sale){

        //     $salespersonname[] = $sale->SalespersonName;
        //     $grossprofit[] = $sale->GrossProfit;
        //     $salerevenue[] = $sale->SaleRevenue;
        //     $Price[] = $sale->Price;
        //     $totalcommission[] = $sale->TotalCommission;

        // }


        $firstrecord = DB::table('SalesOrder')
            ->select('GrossProfit', 'SaleRevenue', 'TotalCommission', 'Price')->whereYear('CreatedOn', '2023')
            ->first();

        // dd($firstrecord);



        $sales = DB::table('SalesOrder')
            ->select('GrossProfit', 'SaleRevenue', 'TotalCommission', 'SalespersonName', 'Price')->whereYear('CreatedOn', '2021')
            ->limit(10)
            ->get();

        // $myorder = DB::table('customers')->join('orders', 'customers.customer_id', '=', 'orders.customer_id')
        // ->select('customers.name', DB::raw('MAX(orders.order_date) AS FirstOrderDate'))->groupBy('customers.name')->get();


        // $myorder1 = DB::table('customers')->join('orders', 'customers.customer_id', '=', 'orders.customer_id')
        // ->join('products', 'products.product_id', '=', 'orders.product_id')
        // ->select('customers.name', 'products.price')->where('products.price', '>', 1000)->get();


        // dd($sales);

        foreach ($sales as $sale) {

            $salespersonname[] = $sale->SalespersonName;
            $grossprofit[] = $sale->GrossProfit;
            $salerevenue[] = $sale->SaleRevenue;
            $Price[] = $sale->Price;
            $totalcommission[] = $sale->TotalCommission;


        }


        return view('charts', [
            'salespersonname' => $salespersonname,
            'grossprofit' => $grossprofit,
            'salerevenue' => $salerevenue,
            'totalcommission' => $totalcommission,
            'firstrecord' => $firstrecord,
            'Price' => $Price
        ]);

    }

    public function checkmethod()
    {

        return redirect()->route('showcharts');
    }

    public function getchart(Request $request)
    {


        $yearchoose = $request->yearchoose ?? null;


        if ($yearchoose > '2024') {
            return redirect()->route('showcharts')->with('error', 'No Data Availabe For This Year');
        }



        $sales = DB::table('SalesOrder')
            ->select('GrossProfit', 'SaleRevenue', 'TotalCommission', 'SalespersonName', 'Price')->whereYear('CreatedOn', $yearchoose)
            ->limit(20)
            ->get()
            ->toArray();

        $firstrecord = DB::table('SalesOrder')
            ->select('GrossProfit', 'SaleRevenue', 'TotalCommission', 'Price')->whereYear('CreatedOn', '2023')
            ->first();


        foreach ($sales as $sale) {

            $salespersonname[] = $sale->SalespersonName;
            $grossprofit[] = $sale->GrossProfit;
            $salerevenue[] = $sale->SaleRevenue;
            $Price[] = $sale->Price;
            $totalcommission[] = $sale->TotalCommission;


        }

        return view('charts', [

            'salespersonname' => $salespersonname,
            'grossprofit' => $grossprofit,
            'salerevenue' => $salerevenue,
            'totalcommission' => $totalcommission,
            'firstrecord' => $firstrecord,
            'Price' => $Price

        ]);
    }

    public function searchperson(Request $request)
    {

        if ($request->searchperson == null) {
            return redirect()->route('showcharts')->with('myerror', 'Please Enter Salesperson Name');
        }
        $firstrecord = DB::table('SalesOrder')
            ->select('GrossProfit', 'SaleRevenue', 'TotalCommission', 'Price')
            ->where('SalespersonName', 'LIKE', '%' . $request->searchperson . '%')
            ->whereYear('CreatedOn', '2023')
            ->first();

        $sales = DB::table('SalesOrder')
            ->select('GrossProfit', 'SaleRevenue', 'TotalCommission', 'SalespersonName')->whereYear('CreatedOn', '2021')
            ->limit(15)
            ->get()
            ->toArray();

        // dd($sales);

        foreach ($sales as $sale) {

            $salespersonname[] = $sale->SalespersonName;
            $grossprofit[] = $sale->GrossProfit;
            $salerevenue[] = $sale->SaleRevenue;
            $totalcommission[] = $sale->TotalCommission;

        }

        // Redirect with flash data

        return view('charts', [
            'salespersonname' => $salespersonname,

            'grossprofit' => $grossprofit,
            'salerevenue' => $salerevenue,
            'totalcommission' => $totalcommission,
            'firstrecord' => $firstrecord
        ]);
    }

    public function saltespersondetail($name)
    {
        \Log::info('Category received: ' . $name);
        dd($name);
    }


    /**
     * @OA\Get(
     *     path="/api/roles",
     *     tags={"Roles"},
     *     summary="Retrieve all roles",
     *     description="This endpoint allows you to retrieve all roles.",
     *     @OA\Response(
     *         response=200,
     *         description="Returns all roles",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="roles", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="RoleID", type="integer", example=1, description="The ID of the role"),
     *                     @OA\Property(property="RoleName", type="string", example="Admin", description="The name of the role"),
     *                     @OA\Property(property="AdminAccess", type="boolean", example=true, description="Whether the role has admin access"),
     *                     @OA\Property(property="CreatePackingList", type="boolean", example=true, description="Whether the role can create packing lists"),
     *                     @OA\Property(property="ProcessPackingList", type="boolean", example=true, description="Whether the role can process packing lists"),
     *                     @OA\Property(property="UpdateSerialNumbers", type="boolean", example=true, description="Whether the role can update serial numbers"),
     *                     @OA\Property(property="DeletePackingList", type="boolean", example=true, description="Whether the role can delete packing lists"),
     *                     @OA\Property(property="ViewPreSale", type="boolean", example=true, description="Whether the role can view pre-sales"),
     *                     @OA\Property(property="CovertPackingList", type="boolean", example=true, description="Whether the role can convert packing lists"),
     *                     @OA\Property(property="EditCommission", type="boolean", example=true, description="Whether the role can edit commissions"),
     *                     @OA\Property(property="EditTransferCost", type="boolean", example=true, description="Whether the role can edit transfer costs"),
     *                     @OA\Property(property="seeCost", type="boolean", example=true, description="Whether the role can see costs"),
     *                     @OA\Property(property="ItemConfigurate", type="boolean", example=true, description="Whether the role can configure items"),
     *                     @OA\Property(property="EditCPCSegments", type="boolean", example=true, description="Whether the role can edit CPC segments"),
     *                     @OA\Property(property="ViewCommission", type="boolean", example=true, description="Whether the role can view commissions"),
     *                     @OA\Property(property="ViewReports", type="boolean", example=true, description="Whether the role can view reports"),
     *                     @OA\Property(property="ViewStats", type="boolean", example=true, description="Whether the role can view stats"),
     *                     @OA\Property(property="SeeHighVolume", type="boolean", example=true, description="Whether the role can see high volume"),
     *                     @OA\Property(property="ITPursuit", type="boolean", example=true, description="Whether the role can pursue IT"),
     *                     @OA\Property(property="RestrictRightsTo", type="boolean", example=true, description="Whether the role can restrict rights to"),
     *                     @OA\Property(property="AlarmCalendar", type="boolean", example=true, description="Whether the role can view the alarm calendar")
     *                 )
     *             )
     *         )
     *     ),
     * 
     *    @OA\Response(
     *        response=404,
     *       description="No roles found",
     *      @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="No roles found.")
     *    )
     * ),
     * 
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while retrieving roles.")
     *         )
     *     )
     * )
     */

    public function getAllRoles()
    {

        $roles = DB::table('roles')
            ->select(
                'roles.RoleID',
                'roles.RoleName',
                'roles.AdminAccess',
                'roles.CreatePackingList',
                'roles.ProcessPackingList',
                'roles.UpdateSerialNumbers',
                'roles.DeletePackingList',
                'roles.ViewPreSale',
                'roles.CovertPackingList',
                'roles.EditCommission',
                'roles.EditTransferCost',
                'roles.seeCost',
                'roles.ItemConfigurate',
                'roles.EditCPCSegments',
                'roles.ViewCommission',
                'roles.ViewReports',
                'roles.ViewStats',
                'roles.SeeHighVolume',
                'roles.ITPursuit',
                'roles.RestrictRightsTo',
                'roles.AlarmCalendar'
            )->get()->map(function ($item) {
                $booleanFields = [
                    'AdminAccess',
                    'CreatePackingList',
                    'ProcessPackingList',
                    'UpdateSerialNumbers',
                    'DeletePackingList',
                    'ViewPreSale',
                    'CovertPackingList',
                    'EditCommission',
                    'EditTransferCost',
                    'seeCost',
                    'ItemConfigurate',
                    'EditCPCSegments',
                    'ViewCommission',
                    'ViewReports',
                    'ViewStats',
                    'SeeHighVolume',
                    'ITPursuit',
                    'RestrictRightsTo',
                    'AlarmCalendar'
                ];

                foreach ($booleanFields as $field) {
                    if (isset($item->$field)) {
                        $item->$field = $item->$field == 1;
                    }
                }

                return $item;
            });


        if ($roles->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No roles found.',
            ], 404);
        }



        return response()->json([
            'status' => 'success',
            'roles' => $roles,
        ], 200);


    }

    /**
     * @OA\Post(
     *    path="/api/findroles",
     *   tags={"Roles"},
     *  summary="Retrieve role by ID",
     * description="This endpoint allows you to retrieve a role by its ID.",
     * @OA\RequestBody(
     *   required=true,
     * @OA\JsonContent(
     *    required={"roleid"},
     *  @OA\Property(property="roleid", type="integer", example=1, description="The ID of the role")
     * )
     * ),
     * @OA\Response(
     *   response=200,
     * description="Returns the role",
     * @OA\JsonContent(
     *   @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="roles", type="array",
     * @OA\Items(
     * @OA\Property(property="RoleID", type="integer", example=1, description="The ID of the role"),
     * @OA\Property(property="RoleName", type="string", example="Admin", description="The name of the role"),
     * @OA\Property(property="AdminAccess", type="boolean", example=true, description="Whether the role has admin access"),
     * @OA\Property(property="CreatePackingList", type="boolean", example=true, description="Whether the role can create packing lists"),
     *    
     * @OA\Property(property="ProcessPackingList", type="boolean", example=true, description="Whether the role can process packing lists"),
     * @OA\Property(property="UpdateSerialNumbers", type="boolean", example=true, description="Whether the role can update serial numbers"),
     * @OA\Property(property="DeletePackingList", type="boolean", example=true, description="Whether the role can delete packing lists"),
     * @OA\Property(property="ViewPreSale", type="boolean", example=true, description="Whether the role can view pre-sales"),
     * @OA\Property(property="CovertPackingList", type="boolean", example=true, description="Whether the role can convert packing lists"),
     * @OA\Property(property="EditCommission", type="boolean", example=true, description="Whether the role can edit commissions"),
     * @OA\Property(property="EditTransferCost", type="boolean", example=true, description="Whether the role can edit transfer costs"),
     * @OA\Property(property="seeCost", type="boolean", example=true, description="Whether the role can see costs"),
     * @OA\Property(property="ItemConfigurate", type="boolean", example=true, description="Whether the role can configure items"),
     * @OA\Property(property="EditCPCSegments", type="boolean", example=true, description="Whether the role can edit CPC segments"),
     * @OA\Property(property="ViewCommission", type="boolean", example=true, description="Whether the role can view commissions"),
     * @OA\Property(property="ViewReports", type="boolean", example=true, description="Whether the role can view reports"),
     * @OA\Property(property="ViewStats", type="boolean", example=true, description="Whether the role can view stats"),
     * @OA\Property(property="SeeHighVolume", type="boolean", example=true, description="Whether the role can see high volume"),
     * @OA\Property(property="ITPursuit", type="boolean", example=true, description="Whether the role can pursue IT"),
     * @OA\Property(property="RestrictRightsTo", type="boolean", example=true, description="Whether the role can restrict rights to"),
     * @OA\Property(property="AlarmCalendar", type="boolean", example=true, description="Whether the role can view the alarm calendar")
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="No role found",
     * @OA\JsonContent(
     *    @OA\Property(property="message", type="string", example="No role found.")
     * )
     * )
     * )
     *    
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="An error occurred while retrieving the role.")
     * )
     * )
     * )
     * 
     * 
     */


    public function getRoleById(Request $request)
    {

        $roleid = $request->input('roleid');

        $roles = DB::table('roles')

            ->select(
                'roles.RoleID',
                'roles.RoleName',
                'roles.AdminAccess',
                'roles.CreatePackingList',
                'roles.ProcessPackingList',
                'roles.UpdateSerialNumbers',
                'roles.DeletePackingList',
                'roles.ViewPreSale',
                'roles.CovertPackingList',
                'roles.EditCommission',
                'roles.EditTransferCost',
                'roles.seeCost',
                'roles.ItemConfigurate',
                'roles.EditCPCSegments',
                'roles.ViewCommission',
                'roles.ViewReports',
                'roles.ViewStats',
                'roles.SeeHighVolume',
                'roles.ITPursuit',
                'roles.RestrictRightsTo',
                'roles.AlarmCalendar'
            )->where('RoleID', $roleid)->get()->map(function ($item) {

                $booleanFields = [
                    'AdminAccess',
                    'CreatePackingList',
                    'ProcessPackingList',
                    'UpdateSerialNumbers',
                    'DeletePackingList',
                    'ViewPreSale',
                    'CovertPackingList',
                    'EditCommission',
                    'EditTransferCost',
                    'seeCost',
                    'ItemConfigurate',
                    'EditCPCSegments',
                    'ViewCommission',
                    'ViewReports',
                    'ViewStats',
                    'SeeHighVolume',
                    'ITPursuit',
                    'RestrictRightsTo',
                    'AlarmCalendar'
                ];

                foreach ($booleanFields as $field) {
                    if (isset($item->$field)) {
                        $item->$field = $item->$field == 1;
                    }
                }

                return $item;
            });


        if ($roles->isEmpty()) {

            return response()->json([
                'status' => 'error',
                'message' => 'No Employee Found',

            ], 404);

        } else {

            return response()->json([
                'status' => 'success',
                'roles' => $roles,

            ], 200);
        }

    }

    /**
     * @OA\Get(
     *    path="/api/submittedsales",
     *  tags={"Submitted Sales"},
     * summary="Retrieve submitted sales",
     * description="This endpoint allows you to retrieve submitted sales.",
     * @OA\Response(
     *  response=200,
     * description="Returns submitted sales",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="sales", type="array",
     * @OA\Items(
     * @OA\Property(property="SaleType", type="string", example="Sale"),
     * @OA\Property(property="RefNo", type="string", example="123456"),
     * @OA\Property(property="SubmittedOn", type="string", format="date", example="2024-11-01"),
     * @OA\Property(property="CustomerName", type="string", example="John Doe"),
     * @OA\Property(property="CustomerID", type="integer", example=1),
     * @OA\Property(property="SpecialPricing", type="boolean", example=true),
     * @OA\Property(property="Pickup", type="boolean", example=true),
     * @OA\Property(property="OrderStatus", type="string", example="Submitted"),
     * @OA\Property(property="SalespersonName", type="string", example="Jane Doe"),
     * @OA\Property(property="TransferCost", type="number", format="float", example=100.00),
     * @OA\Property(property="SaleRevenue", type="number", format="float", example=5000.00),
     * @OA\Property(property="Price", type="number", format="float", example=6000.00)
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="No sales found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="No sales found.")
     * )
     * )
     * ),
     *  
     * 
     */

    public function getSubmittedSales()
    {
        $sales = DB::table('salesorder')
            ->select(
                'SaleType',
                'RefNo',
                'SubmittedOn',
                'CustomerName',
                'CustomerID',
                'SpecialPricing',
                'Pickup',
                'OrderStatus',
                'SalespersonName',
                'TransferCost',
                'SaleRevenue',
                'Price',
            )
            ->orderBy('CreatedBy', 'DESC')
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'sales' => $sales
        ], 200);

        if ($sales->isEmpty()) {

            return response()->json([
                'status' => 'error',
                'message' => 'No sales found.'
            ], 404);
        }

    }


   /**
    * @OA\Get(
    * path="/api/salestypes",
    * tags={"Sales Types"},
    * summary="Retrieve sales types",
    * description="This endpoint allows you to retrieve sales types.",
    * @OA\Response(
    * response=200,
    * description="Returns sales types",
    * @OA\JsonContent(
    * @OA\Property(property="status", type="string", example="success"),
    * @OA\Property(property="Sales Types", type="array",
    * @OA\Items(
    * @OA\Property(property="salevalue", type="string", example="cash_n_serv", description="The value of the sale type"),
    * @OA\Property(property="saletype", type="string", example="Cash Deal With Service", description="The name of the sale type")
    * )
    * )
    * )
    * ),
    * @OA\Response(
    * response=404,
    * description="No sales types available",
    * @OA\JsonContent(
    * @OA\Property(property="message", type="string", example="No sales types available.")
    * )
    * )
    * )
    
    */



    public function getSalesType()
    {
        //key should match the value in the database that are stored in the SaleType column
        //Full name will be associalted with the key in map functin

        $saletypeMapping = [
            'cash_n_serv' => 'Cash Deal With Service',
            'cash_w_serv' => 'Cash Deal Without Service',
            'cpc_bw' => 'Lease CPC Black and White',
            'cpc_bwc' => 'Lease CPC Black and White + Color',
            'cpc_c' => 'Lease CPC Color',
            'dealer_install' => 'Dealer Install',
            'demo' => 'Demo Sale',
            'lease' => 'Lease Sale',
            'none' => 'No Sale',
            'rental' => 'Rental Sale',
            'snap' => 'SNAP'
        ];

        $salestype = DB::table('salesorder')
            ->select('SaleType')
            ->distinct()
            ->get();


        $newsaletype = $salestype->map(function ($item) use ($saletypeMapping) {
            return [
                'salevalue' => $item->SaleType,
                'saletype' => $saletypeMapping[$item->SaleType]?? 'Unknown'
            ];
        });

        if ($salestype->isEmpty()) {
            return response()->json([
                'message' => 'No sales types available.'
            ], 404);


        } else {
            return response()->json([
                'status' => 'success',
                'Sales Types' => $newsaletype
            ]);
        }



    }

    /**
     * @OA\Get(
     *  path="/api/presales",
     * tags={"Pre Sales"},
     * summary="Retrieve pre-sales",
     *  description="This endpoint allows you to retrieve pre-sales.",
     * @OA\Response(
     * response=200,
     * description="Returns pre-sales",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="Pre Sales", type="array",
     * @OA\Items(
     * @OA\Property(property="Refno", type="string", example="123456"),
     * @OA\Property(property="CreatedOn", type="string", format="date", example="2024-11-01"),
     * @OA\Property(property="CustomerName", type="string", example="John Doe"),
     * @OA\Property(property="CustomerID", type="integer", example=1),
     * @OA\Property(property="SpecialPricing", type="boolean", example=true),
     * @OA\Property(property="Pickup", type="boolean", example=true),
     * @OA\Property(property="SalespersonName", type="string", example="Jane Doe"),
     * @OA\Property(property="TransferCost", type="number", format="float", example=100.00),
     * @OA\Property(property="SaleRevenue", type="number", format="float", example=5000.00),
     *  @OA\Property(property="Price", type="number", format="float", example=6000.00),
     * @OA\Property(property="TotalCommission", type="number", format="float", example=1000.00),
     * @OA\Property(property="parent_ref_no", type="string", example="123456"),
     * @OA\Property(property="TotalProfit", type="number", format="float", example=1500.00)
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="No Pre Sales Found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="No Pre Sales Found")
     * )
     * )
     * )
     * 
     */

    public function getPreSales()
    {

        $presales = DB::table('SalesDraft')
            ->select(
                'Refno',
                'CreatedOn',
                'CustomerName',
                'CustomerID',
                'SpecialPricing',
                'Pickup',
                'SalespersonName',
                'TransferCost',
                'SaleRevenue',
                'Price',
                'TotalCommission',
                'parent_ref_no',
                DB::raw('CBECommission + GrossProfit AS TotalProfit')
            )->limit(20)->get();

        if ($presales->isEmpty()) {

            return response()->json([
                'message' => 'No Pre Sales Found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'Pre Sales' => $presales
        ]);


    }

    /**
     * @OA\Post(
     * path="/api/commissiontracker",
     * tags={"Commission Tracker"},
     * summary="Retrieve commission tracker",
     * description="This endpoint allows you to retrieve the commission tracker.",
     * @OA\Parameter(
     * name="year",
     * in="query",
     * description="The year for which to retrieve the commission tracker",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example=2024
     * )
     * ),
     * @OA\Parameter(
     * name="month",
     * in="query",
     * description="The month for which to retrieve the commission tracker",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example=11
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Returns the commission tracker",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="Commission Tracker", type="array",
     *  @OA\Items(
     * @OA\Property(property="RefNo", type="string", example="123456"),
     * @OA\Property(property="CustomerName", type="string", example="John Doe"),
     * @OA\Property(property="SalesQuote", type="string", example="123456"),
     * @OA\Property(property="SalesRepID", type="integer", example=1),
     * @OA\Property(property="TotalProfit", type="number", format="float", example=1500.00),
     * @OA\Property(property="TotalCommission", type="number", format="float", example=1000.00),
     * @OA\Property(property="InvoiceNumber", type="string", example="123456"),
     * @OA\Property(property="InvoiveDate", type="string", format="date", example="2024-11-01")
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="No Data Found For This Month",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="No Data Found For This Month")
     * )
     * )
     * )
     * 
     * 
     * 
     */




    public function commissionTracker(Request $request)
    {

        $year = $request->input('year');
        $month = $request->input('month');

        $commissionTracker = DB::table('salesorder')
            ->join('CellFastInvoices', 'salesorder.RefNo', '=', 'CellFastInvoices.RefNo')
            ->select(
                'salesorder.RefNo',
                'salesorder.CustomerName',
                'salesorder.SalesQuote',
                'salesorder.SalesRepID',
                DB::raw('SUM(salesorder.GrossProfit + salesorder.CBECommission) AS TotalProfit'),
                'salesorder.TotalCommission',
                'CellFastInvoices.InvoiceNumber',
                'CellFastInvoices.Date',
                'salesorder.salesPersonName'
            )
            ->whereYear('salesorder.CreatedOn', $year)->whereMonth('salesorder.CreatedOn', $month)
            ->groupBy(
                'salesorder.RefNo',
                'salesorder.CustomerName',
                'salesorder.SalesQuote',
                'salesorder.SalesRepID',
                'salesorder.TotalCommission',
                'CellFastInvoices.InvoiceNumber',
                'CellFastInvoices.Date',
                'salesorder.salesPersonName'
            )
            ->get()->map(function ($item) {

                return [
                    'RefNo' => $item->RefNo,
                    'CustomerName' => $item->CustomerName,
                    'SalesQuote' => $item->SalesQuote,
                    'SalesRepID' => $item->SalesRepID,
                    'SalesPersonName' => $item->salesPersonName,
                    'TotalProfit' => $item->TotalProfit,
                    'TotalCommission' => $item->TotalCommission,
                    'InvoiceNumber' => $item->InvoiceNumber,
                    'InvoiveDate' => $item->Date,

                ];
            });


        if ($commissionTracker->count() == 0) {
            return response()->json([
                'message' => 'No Data Found For This Month',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'Commission Tracker' => $commissionTracker
        ], 200);
    }


    /**
     * /**
     * @OA\Get(
     *      path="/api/existedcustomer",
     *      tags={"Existed Customer"},
     *      summary="Retrieve existed customer list",
     *      description="This endpoint allows you to retrieve existed customer list of 50",
     *      @OA\Response(
     *          response=200,
     *          description="Returns existed customer list",
     *         @OA\JsonContent(
     *         @OA\Property(property="status", type="string", example="success"),
     *         @OA\Property(property="Existed Customer", type="array",
     *         @OA\Items(
     *        @OA\Property(property="CustomerName", type="string", example="John Doe"),
     *       @OA\Property(property="ShipToAddress", type="string", example="123 Main St"),
     *      @OA\Property(property="ShipToCity", type="string", example="New York"),
     *    @OA\Property(property="ShipToState", type="string", example="NY"),
     *  @OA\Property(property="SalespersonName", type="string", example="Jane Doe"),
     * @OA\Property(property="BillToPhone", type="string", example="123-456-7890"),
     * @OA\Property(property="BillToFax", type="string", example="123-456-7890"),
     * @OA\Property(property="Email", type="string", example="[email protected]"),
     * @OA\Property(property="BillToZip", type="string", example="12345"),
     * @OA\Property(property="BranchName", type="string", example="New York")
     * 
     * 
     *        
     *      )
     *   )
     * )
     * ),
     *       @OA\Response(
     *                   response=404,
     *                   description="No Existed Customer Found",
     *       @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="No Existed Customer Found")
     *                )
     *              )
     * )
     * 
     *     ),
     *     )
     */

    public function existedCustomer()
    {

        $existedcustomer = DB::table('salesorder')
            ->join('Branches', 'salesorder.BranchID', '=', 'Branches.BranchId')
            ->select(
                'salesorder.CustomerName',
                'salesorder.ShipToAddress',
                'salesorder.ShipToCity',
                'salesorder.ShipToState',
                'salesorder.SalespersonName',
                'salesorder.BillToPhone',
                'salesorder.BillToFax',
                'salesorder.Email',
                'salesorder.BillToZip',
                'Branches.BranchName'
            )
            ->whereNotNull('salesorder.CustomerName')
            ->whereNotNull('salesorder.ShipToAddress')
            ->whereNotNull('salesorder.ShipToCity')
            ->whereNotNull('salesorder.ShipToState')
            ->whereNotNull('salesorder.SalespersonName')
            ->whereNotNull('salesorder.BillToPhone')
            ->whereNotNull('salesorder.BillToFax')
            ->where('salesorder.BillToFax', '!=', '')
            ->whereNotNull('salesorder.Email')
            ->whereNotNull('salesorder.BillToZip')
            ->whereNotNull('Branches.BranchName')
            ->orderBy('salesorder.Email', 'DESC')
            ->limit(50)
            ->get();


        //  $asa =    count(value: $existedcustomer);


        if ($existedcustomer->isEmpty()) {

            return response()->json([
                'message' => 'No Existed Customer Found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'Existed Customer' => $existedcustomer
        ], 200);
    }




    public function updateSales($RefNo)
    {

        $user = DB::table('salesorder')
            ->where('RefNo', $RefNo)->first();

        if ($user) {

            DB::table('SalesOrder')
                ->where('RefNo', $RefNo)
                ->update([
                    'SalespersonName' => 'John Doe',
                    'TransferCost' => 100,
                    'SaleRevenue' => 5000,
                    'Price' => 1000,
                    'TotalCommission' => 200
                ]);

            $updatedUser = DB::table('SalesOrder')->where('RefNo', $RefNo)->first();


            return response()->json([
                'status' => 'success',
                'user' => $updatedUser

            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No User Found'
            ]);
        }
    }

}


