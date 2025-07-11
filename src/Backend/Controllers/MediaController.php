<?php

namespace VnCoder\Backend\Controllers;
use VnCoder\Backend\Controllers\BackendController;
use VnCoder\Backend\Models\VnMedia;
use Illuminate\Http\Request;

class MediaController extends BackendController
{
    public function Index_Action()
    {
        $this->metaData->title = 'Media Manager';
        $this->setData['medias'] = VnMedia::getMedia(24);
        $this->usingFormEditor = true;
        return $this->views('media.index');
    }

    public function Loader_Action()
    {
        $action = getParam('action', 'input');
        $medias = VnMedia::getMedia(29);
        return view('backend::core.media', ['medias' => $medias , 'action' => $action]);
    }

    public function Loader_Action_Submit(Request $request)
    {
        if ($request->hasFile('vn_media')) {
            return VnMedia::doUpload($request);
        }
        $type = getParam('type', '');
        if ($type == 'delete') {
            return VnMedia::removeMedia();
        }
        return false;
    }
}
