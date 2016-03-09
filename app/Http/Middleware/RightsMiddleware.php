<?php

namespace App\Http\Middleware;

use Closure;

use Auth;
use App\Libraries\CHpermissions\CHpermissions;
use App\BaseContracts;
use App\FilmOwners;

class RightsMiddleware
{
    private $rightsPermission = [];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $current_menu = '';
        $authUser = Auth::user();
        $authAccount = $authUser->account;
        $platformID = $authAccount->platforms_id;
		$CHpermissions = new CHpermissions();

		if($CHpermissions->isCPPL()){
            $this->rightsPermission['action'] = 'CPPL';
            $this->rightsPermission['showCp'] = true;
            $this->rightsPermission['havePlatform'] = true;
			$baseContactID = $request->film->baseContract->channelsContracts->where('cc_channels_contracts.channel_id', $platformID)->first()->id;
			if($baseContactID > 0){
                $ownerInfo = $request->film->filmOwners->where('owner_id', $platformID)->first();
				$actionType = 'You are now acting as a <span class="proxBold">Store.</span>';
                if ($CHpermissions->isAllowAct('update', 'rights', $ownerInfo->role)){
                    $baseContactID = $request->film->basecontract->id;
                    if($baseContactID > 0){
                        $this->rightsPermission['actionType'] = 'You are now acting as a <span class="proxBold">Store</span> — <a class="cp" onclick="changeCPPL(\'CP\',\''.$request->film->id.'\')">Change to Content Provider</a>.';
					}
                    $this->rightsPermission['showCp'] = false;
                }
                else{
                    $this->rightsPermission['action'] = 'CPPL';
                    $this->rightsPermission['showCp'] = 'true';
                }
			}
            else{
                $this->rightsPermission['havePlatform'] = false;

            }

            if ($this->rightsPermission['showCp']){
                $baseContactID = $request->film->basecontract->id;
                if ($baseContactID > 0){
                    if ($this->rightsPermission['havePlatform'])
                        $this->rightsPermission['actionType'] = 'You are now acting as a <span class="proxBold">Content Provider</span> â€” <a class="cp" onclick="changeCPPL(\'PL\',\''.$request->film->id.'\')">Change to Store</a>.';
                    else
                        $this->rightsPermission['actionType'] = 'You are now acting as a <span class="proxBold">Content Provider</span>.';
                }
            }			
		}
        else if($CHpermissions->isCP()){
            $baseContactID = $request->film->basecontract->id;
            if ($baseContactID > 0){
                $this->rightsPermission['action'] = 'CP';
                $this->rightsPermission['message'] = '';
            }
            else{
                $this->rightsPermission['action'] = 'CP';
                $this->rightsPermission['message'] = 'There is no any contract, please contact with administrator (support@cinehost.com)';
            }
        }
        else if ($CHpermissions->isPL()){
                $baseContactID = $request->film->basecontract->id;
                if($baseContactID > 0){
                    $this->rightsPermission['action'] = 'PL';
                    $this->rightsPermission['message'] = '';
                }
                else{
                    $this->rightsPermission['action'] = 'PL';
                    $this->rightsPermission['message'] = 'There is no any contract, please contact with administrator (support@cinehost.com)';
                }
        }	
		
        $request->merge(["rightsPermission" => $this->rightsPermission]);

        return $next($request);
    }
}
