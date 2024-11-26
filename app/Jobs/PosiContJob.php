<?php

namespace App\Jobs;

 
use Illuminate\Bus\Queueable; 
use App\Http\Controllers\{BlacsolController, SamsaraController};
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels; 
use Illuminate\Support\Facades\Log;

class PosiContJob extends Command implements ShouldQueue
{
    
    use Queueable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:PosiContJob';

    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';


    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::channel()->info('[*]['.date('H:i:s')."] Iniciando PosiContJob... .\r\n");

        try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			
			for ($i=0; $i < count($data)-1; $i++) { 
				$BlacSol = new BlacsolController(
					$data[$i]['username'],	// username
					$data[$i]['imei'],	// imei
					$data[$i]['latitude'],	// latitude
					$data[$i]['longitude'],	// longitude
					$data[$i]['altitude'],	// altitude
					$data[$i]['speed'],	// speed
					$data[$i]['azimuth'],	// azimuth
					$data[$i]['odometer'],	// odometer
					$data[$i]['dateTimeUTC']	// dateTimeUTC
				);
				
				$BlacSol->PosiCont();
				unset($BlacSol);
			} 


			return response()->json([
				'status' => true,
				'code'   => 200
			]);
		} catch (\Exception $th) {
			return response()->json([
				'status' => false,
				'code'   => 500,
				'error'  => $th->getMessage()
			]);
		}

        
        Log::channel()->info('[*]['.date('H:i:s')."] Finalizando proceso de PosiContJob.. .\r\n");
    }
}