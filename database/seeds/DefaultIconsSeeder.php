<?php

use App\Models\Icon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DefaultIconsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Icon::all()->count()) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Icon::truncate();

            if (is_dir(public_path('img/icons'))) {
                $icons = collect(File::allFiles(public_path('img/icons')));
                $iconsSorted = $icons->sort(function ($a, $b) {
                    /** @var \Symfony\Component\Finder\SplFileInfo $a */
                    /** @var \Symfony\Component\Finder\SplFileInfo $b */
                    if ($a->getFilename() === $b->getFilename()) return 0;

                    return $a->getFilename() < $b->getFilename() ? -1 : 1;
                });
                /** @var SplFileInfo $icon */
                foreach ($iconsSorted as $icon) {
                    Storage::putFileAs('public/img/icons', new \Illuminate\Http\File($icon->getRealPath()), $icon->getFilename());
                    Icon::insert([
                        'path' => 'img/icons/'.$icon->getFilename(),
                        'filename' => $icon->getFilename(),
                        'is_deletable' => 0
                    ]);
                }
            }
        }
    }
}
