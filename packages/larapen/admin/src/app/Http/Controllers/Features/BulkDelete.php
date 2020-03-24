<?php

namespace Larapen\Admin\app\Http\Controllers\Features;

trait BulkDelete
{
    public function bulkDelete()
	{
		$this->xPanel->hasAccessOrFail('delete');
		
		$redirectUrl = $this->xPanel->route;
		
		if (!request()->has('entryId')) {
			return \Redirect::to($redirectUrl);
		}
		
		try {
			$ids = request('entryId');
			foreach ($ids as $id) {
				$res = $this->xPanel->delete($id);
			}
			
			\Alert::success(trans('admin::messages.The items were been deleted successfully.'))->flash();
		} catch (\Exception $e) {
			\Alert::error($e->getMessage())->flash();
		}
		
		return \Redirect::to($redirectUrl);
	}
}
