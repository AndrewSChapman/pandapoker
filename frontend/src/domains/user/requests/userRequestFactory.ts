import { ApiClient } from '@/domains/shared/apiClient';
import { CreateUserRequest } from '@/domains/user/requests/createUserRequest';
import { UpdateUserRequest } from '@/domains/user/requests/updateUserRequest';
import { UserListRequest } from '@/domains/user/requests/userListRequest';
import { AssumeUserRequest } from '@/domains/user/requests/assumeUserRequest';
import { GetUserRequest } from '@/domains/user/requests/getUserRequest';

export class UserRequestFactory {
    private readonly apiClient: ApiClient;

    constructor(apiClient: ApiClient) {
        this.apiClient = apiClient;
    }

    public get assumeUser(): AssumeUserRequest {
        return new AssumeUserRequest(this.apiClient);
    }

    public get createUser(): CreateUserRequest {
        return new CreateUserRequest(this.apiClient);
    }

    public get getUser(): GetUserRequest {
        return new GetUserRequest(this.apiClient);
    }

    public get updateUser(): UpdateUserRequest {
        return new UpdateUserRequest(this.apiClient);
    }

    public get userList(): UserListRequest {
        return new UserListRequest(this.apiClient);
    }
}
