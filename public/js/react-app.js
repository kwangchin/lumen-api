var UserComponent = React.createClass({
    loadUsersFromServer: function() {
        this.setState({loading: true});
        $.ajax({
            url: this.props.url,
            dataType: 'json',
            cache: false,
            success: function(data) {
                if (typeof data.error !== 'undefined') {
                    this.setState({error: data.error});
                } else {
                    this.setState({data: data, ajaxError: null});
                }
            }.bind(this),
            error: function(xhr, status, err) {
                this.setState({ajaxError: err.toString()});
            }.bind(this),
            complete: function() {
                this.setState({loading: false});
                setTimeout(this.loadUsersFromServer, this.props.pollInterval);
            }.bind(this)
        });
    },
	getInitialState: function() {
		return {
			loading: false,
			error: null,
			ajaxError: null,
			data: []
		};
	},
	componentDidMount: function() {
		this.loadUsersFromServer();
	},
    render: function() {
		var loading;
		if (this.state.loading) {
			loading = <div className="box-loading"><i className="fa fa-spinner fa-spin"></i></div>
		}
        return (
            <div className="box">
                <h2>Lumen API Test Users</h2>
                {loading}
                <table id="users">
                    <thead>
                        <tr>
                            <th>UID</th>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        {this.state.data.map(user => (
                            <UserElement key={user.uid} user={user} />
                        ))}
                    </tbody>
                </table>
            </div>
        )
    }
});

var UserElement = React.createClass({
    render: function() {
        return (
            <tr>
                <td>{this.props.user.uid}</td>
                <td>{this.props.user.email}</td>
                <td>{this.props.user.name}</td>
                <td>{moment(this.props.user.created_at).fromNow()}</td>
                <td>{moment(this.props.user.updated_at).fromNow()}</td>
            </tr>
        );
    }
});

ReactDOM.render(
    <UserComponent url="/v1/users" pollInterval={10000} />,
    document.getElementById('container')
);
