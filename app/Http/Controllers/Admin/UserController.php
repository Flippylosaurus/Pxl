<?php

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Admin\UpdateUser;
    use App\User;
    use Illuminate\Http\Request;

    /**
     * Class UserController
     *
     * @package App\Http\Controllers\Admin
     */
    class UserController extends Controller {
        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function getView() {
            $usersPagination = User::paginate(15);

            $usersPagination->getUrlRange(0, $usersPagination->lastPage());

            return view('admin.users', [
                'pagination' => $usersPagination
            ]);

        }

        /**
         * @param Request $request
         * @param User    $user
         *
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function getEditView(Request $request, User $user) {
            return view('admin.users.edit', [
                'user' => $user
            ]);
        }

        /**
         * @param UpdateUser $request
         *
         * @param User       $user
         *
         * @return \Illuminate\Http\RedirectResponse
         */
        public function update(UpdateUser $request, User $user) {

            if ($request->has('new_password')) {
                $user->setPassword($request->new_password);
            }

            if ($user->hasTwoFactorAuth()) {
                if (!$request->has('2fa_status')) {
                    $user->twoFactorToken = null; // Clear 2fa from account
                }
            }

            $user->fill($request->only(['username', 'email']));
            $user->rank   = $request->rank;
            $user->active = $request->has('enabled');
            $user->saveOrFail();
            return back()->with('success', trans('admin.users.edit.updated'));
        }

        /**
         * @param Request $request
         * @param User    $user
         *
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function getDeleteView(Request $request, User $user) {
            return view('admin.users.delete', [
                'user' => $user
            ]);
        }

        /**
         * @param Request $request
         * @param User    $user
         *
         * @return \Illuminate\Http\RedirectResponse
         */
        public function deleteUser(Request $request, User $user) {
            $user->delete();
            return redirect(route('admin/users'))->with('success', trans('admin.users.deleted'));
        }
    }
