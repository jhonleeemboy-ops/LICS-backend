<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $client
 * @property-read \App\Models\User|null $lawyer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appointment whereUpdatedAt($value)
 */
	class Appointment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $chat_session_id
 * @property string $sender
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ChatSession $session
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereChatSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereUpdatedAt($value)
 */
	class ChatMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $started_at
 * @property string|null $ended_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatSession whereUserId($value)
 */
	class ChatSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $specialization
 * @property string $license_no
 * @property numeric|null $availability
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawyerProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawyerProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawyerProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawyerProfile whereAvailability($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawyerProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawyerProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawyerProfile whereLicenseNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawyerProfile whereSpecialization($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawyerProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawyerProfile whereUserId($value)
 */
	class LawyerProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatSession> $chatSessions
 * @property-read int|null $chat_sessions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Appointment> $clientAppointments
 * @property-read int|null $client_appointments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Appointment> $lawyerAppointments
 * @property-read int|null $lawyer_appointments_count
 * @property-read \App\Models\LawyerProfile|null $lawyerProfile
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

