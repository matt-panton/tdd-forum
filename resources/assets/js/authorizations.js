let authorizations = {
    updateReply(reply) {
        return reply.user_id === window.App.user.id
    },
    updateThread(thread) {
        return thread.user_id === window.App.user.id
    },
    owns(model, property = 'user_id') {
        return model[property] === window.App.user.id
    },
    isAdmin() {
        return ['matt_panton'].includes(window.App.user.name)
    }
}

export default authorizations
