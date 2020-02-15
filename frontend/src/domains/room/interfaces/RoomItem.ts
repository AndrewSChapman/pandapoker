import { RoomParticipant } from '@/domains/room/interfaces/RoomParticipant';
import { RoomVote } from '@/domains/room/interfaces/RoomVote';

export interface RoomItem {
    id: string;
    name: string;
    created_by_user_id: string;
    vote_options: number[];
    votes: RoomVote[];
    participants: RoomParticipant[];
    voting_open: boolean;
    created_at: number;
    updated_at: number;
    winning_vote?: number;
}

export function getBlankRoom(): RoomItem {
    return {
        id: '0',
        name: 'Unknown',
        created_by_user_id: '0',
        vote_options: [],
        votes: [],
        participants: [],
        voting_open: false,
        created_at: 0,
        updated_at: 0,
    };
}
